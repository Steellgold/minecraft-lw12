<?php

namespace hackaton;

use hackaton\game\Game;
use hackaton\lib\customies\Customies;
use hackaton\lib\GameLib;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

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
     * @return Game[]
     */
    public function getGames(): array {
        return $this->games;
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader {
        return self::$instance;
    }
}