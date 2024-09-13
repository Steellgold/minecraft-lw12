<?php

namespace hackaton\event;

use hackaton\game\Game;
use hackaton\Loader;
use pocketmine\event\plugin\PluginEvent;

abstract class GameEvent extends PluginEvent {

    /**
     * @param Game $game
     * @param int $time
     */
    public function __construct(private readonly Game $game, private readonly int $time) {
        parent::__construct(Loader::getInstance());
    }

    /**
     * @return Game
     */
    public function getGame(): Game {
        return $this->game;
    }

    /**
     * @return int
     */
    public function getTime(): int {
        return $this->time;
    }
}