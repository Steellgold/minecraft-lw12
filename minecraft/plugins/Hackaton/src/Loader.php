<?php

namespace hackaton;

use hackaton\command\LobbyCommand;
use hackaton\lib\customies\Customies;
use hackaton\lib\GameLib;
use hackaton\listener\PlayerListener;
use hackaton\manager\EntityManager;
use hackaton\manager\ItemManager;
use hackaton\player\GAPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\Position;
use pocketmine\world\World;

class Loader extends PluginBase {

    /** @var string */
    public const PREFIX = TF::DARK_GREEN . "[" . TF::GREEN . "Hackaton" . TF::DARK_GREEN . "] " . TF::WHITE;

    /** @var Loader */
    private static Loader $instance;

    /** @var GameLib[] */
    private array $libs = [];

    /**
     * @return void
     */
    protected function onLoad(): void {
        self::$instance = $this;

        // Register libs
        $this->registerLib(new Customies());

        // Save config
        $this->saveResource("config.yml");
        $this->saveResource("laser-game.yml", true);
    }

    /**
     * @return void
     */
    protected function onEnable(): void {
        $title =
            "\n" . TF::DARK_GREEN . "  _    _            _         _              " .
            "\n" . TF::DARK_GREEN . " | |  | |          | |       | |             " .
            "\n" . TF::DARK_GREEN . " | |__| | __ _  ___| | ____ _| |_ ___  _ __  " .
            "\n" . TF::DARK_GREEN . " |  __  |/ _` |/ __| |/ / _` | __/ _ \| '_ \ " .
            "\n" . TF::DARK_GREEN . " | |  | | (_| | (__|   < (_| | || (_) | | | |" .
            "\n" . TF::DARK_GREEN . " |_|  |_|\__,_|\___|_|\_\__,_|\__\___/|_| |_|" .
            "\n";

        $this->getLogger()->info($title);

        $this->loadLibs();
        $this->loadListeners();
        $this->loadCommands();

        EntityManager::getInstance()->initialize();
        ItemManager::getInstance()->initialize();

        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            foreach ($world->getEntities() as $entity) $entity->flagForDespawn();
        }
    }

    /**
     * @return void
     */
    protected function onDisable(): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if (!$player instanceof GAPlayer) continue;

            $player->quitServer();
        }
    }

    /**
     * @param GameLib $lib
     * @return void
     */
    private function registerLib(GameLib $lib): void {
        $this->libs[] = $lib;
    }

    /**
     * @return void
     */
    private function loadLibs(): void {
        foreach ($this->libs as $lib) $lib->onEnable($this);
    }

    /**
     * @return void
     */
    private function loadListeners(): void {
        new PlayerListener();
    }

    /**
     * @return void
     */
    private function loadCommands(): void {
        $this->getServer()->getCommandMap()->registerAll("hackaton", [
            new LobbyCommand()
        ]);
    }

    /**
     * @return Config
     */
    public function getLaserGameConfig(): Config {
        return new Config($this->getDataFolder() . "laser-game.yml", Config::YAML);
    }

    /**
     * @param string $path
     * @return void
     */
    private function deleteFolder(string $path): void {
        $dh = opendir($path);

        while ($file = readdir($dh)) {
            if ($file === "." || $file === "..") continue;
            $fullPath = $path . "/" . $file;

            if (is_dir($fullPath)) {
                $this->deleteFolder($fullPath);
            } else {
                @unlink($fullPath);
            }
        }

        closedir($dh);

        @rmdir($path);
    }

    /**
     * @return Position
     */
    public function getLobbySpawn(): Position {
        $config = $this->getConfig();

        return new Position(
            $config->getNested("lobby.spawn.x"),
            $config->getNested("lobby.spawn.y"),
            $config->getNested("lobby.spawn.z"),
            $this->getServer()->getWorldManager()->getWorldByName($config->getNested("lobby.world"))
        );
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader {
        return self::$instance;
    }
}