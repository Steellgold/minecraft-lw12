<?php

namespace hackaton\task\async;

use hackaton\Loader;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

abstract class RequestAsync extends AsyncTask {

    /** @var string */
    private string $id;

    /** @var string */
    private string $url;

    /** @var string */
    private string $apiKey;

    public function __construct() {
        $this->id = uniqid();
        $config = Loader::getInstance()->getConfig();
        $this->url = $config->getNested("api.url");
        $this->apiKey = $config->getNested("api.key");
        Server::getInstance()->getAsyncPool()->submitTask($this);
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getApiKey(): string {
        return $this->apiKey;
    }

    /**
     * @return void
     */
    public function onCompletion(): void {
        RequestPool::execute($this->getId(), $this->getResult());
    }
}