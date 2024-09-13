<?php

namespace hackaton\task;

use hackaton\game\Game;
use hackaton\Loader;
use pocketmine\scheduler\Task;

abstract class GameTask extends Task {

    /** @var int */
    private int $time = 0;

    /**
     * @param Game $game
     */
    public function __construct(private readonly Game $game) {
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function onRun(): void {
        $this->time++;
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