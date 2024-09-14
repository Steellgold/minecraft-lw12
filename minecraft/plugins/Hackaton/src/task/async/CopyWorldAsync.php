<?php

namespace hackaton\task\async;

use Closure;
use pocketmine\Server;

class CopyWorldAsync extends GameAsyncTask {

    /** @var string */
    private string $uuid;

    /**
     * @param string $id
     * @param Closure $callback
     */
    public function __construct(private readonly string $id, private Closure $callback) {
        $this->uuid = uniqid();
        RequestPool::add($this->uuid, $callback);
        parent::__construct();
    }

    /**
     * @return void
     */
    public function onRun(): void {
        // Copy world from the /worlds folder and paste it to the /worlds folder with the new name
        // We don't have access to the main thread here, so we can't use the PocketMine API
        // We can only use PHP functions. The folder name is the $this->id

        $worlds = scandir("./worlds");

        // Get world folder name equals to $this->id
        $world = array_filter($worlds, function ($world) {
            return $world === $this->id;
        });

        if (count($world) === 0) {
            $this->setResult(null);
            return;
        }

        $worldName = uniqid($this->id . "_");

        $this->copyFolder("./worlds/{$this->id}", "./worlds/{$worldName}");

        $this->setResult($worldName);
    }

    /**
     * @return void
     */
    public function onCompletion(): void {
        $callback = RequestPool::get($this->uuid);
        if ($callback === null) {
            return;
        }

        $worldName = $this->getResult();

        // Load the world with the new name
        Server::getInstance()->getWorldManager()->loadWorld($worldName);

        $callback($worldName);
        RequestPool::remove($this->uuid);
    }

    /**
     * @param string $source
     * @param string $destination
     * @return void
     */
    private function copyFolder(string $source, string $destination): void {
        if (is_dir($source)) {
            @mkdir($destination);
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $this->copyFolder("$source/$file", "$destination/$file");
                }
            }
        } else {
            copy($source, $destination);
        }
    }
}