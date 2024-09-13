<?php

namespace hackaton;

use hackaton\game\Game;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

    /** @var Loader  */
    public static Loader $instance;

    /** @var Game[] */
    private array $games = [];

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