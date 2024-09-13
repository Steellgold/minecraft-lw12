<?php

namespace hackaton;

use hackaton\game\Game;
use hackaton\lib\customies\Customies;
use hackaton\lib\GameLib;
use hackaton\listener\PlayerListener;
use hackaton\manager\ItemManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

    /** @var string */
    public const PREFIX = TF::DARK_GREEN . "[" . TF::GREEN . "Hackaton" . TF::DARK_GREEN . "] " . TF::WHITE;

    /** @var Loader */
    private static Loader $instance;

    /** @var GameLib[] */
    private array $libs = [];

    /** @var Game[] */
    private array $games = [];

    /**
     * @return void
     */
    protected function onLoad(): void {
        self::$instance = $this;

        // Register libs
        $this->registerLib(new Customies());

        // Save config
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

        ItemManager::getInstance()->initialize();
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
     * @return Game[]
     */
    public function getGames(): array {
        return $this->games;
    }

    /**
     * @return Config
     */
    public function getLaserGameConfig(): Config {
        return new Config($this->getDataFolder() . "laser-game.yml", Config::YAML);
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader {
        return self::$instance;
    }
}