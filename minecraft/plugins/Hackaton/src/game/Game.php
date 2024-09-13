<?php

namespace hackaton\game;

class Game {

    /** @var int */
    public const MODE_WAITING = 0;

    /** @var int */
    public const MODE_STARTING = 1;

    /** @var int */
    public const MODE_RUNNING = 2;

    /** @var int */
    public const MODE_FINISHED = 3;

    /** @var int */
    private int $mode = self::MODE_WAITING;

    /** @var Team[] */
    private array $teams = [];

    public function __construct() { }

    /**
     * @return int
     */
    public function getMode(): int {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function setMode(int $mode): void {
        $this->mode = $mode;
    }

    /**
     * @return array
     */
    public function getTeams(): array {
        return $this->teams;
    }
}