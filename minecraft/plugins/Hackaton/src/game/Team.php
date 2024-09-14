<?php

namespace hackaton\game;

use hackaton\player\GAPlayer;

class Team {

    /** @var int */
    public const TYPE_SOLO = 1;

    /** @var int */
    public const TYPE_DUO = 2;

    /** @var int */
    public const TYPE_TRIO = 3;

    /** @var int */
    public const TYPE_SQUAD = 4;

    /** @var GAPlayer[] */
    private array $players = [];

    /**
     * @param int $type
     * @param string $name
     */
    public function __construct(private readonly int $type, private readonly string $name) { }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPlayers(): array {
        return $this->players;
    }

    /**
     * @param GAPlayer $player
     * @return void
     */
    public function addPlayer(GAPlayer $player): void {
        $this->players[] = $player;
    }

    /**
     * @return int
     */
    public function getPlayerCount(): int {
        return count($this->players);
    }

    /**
     * @return int
     */
    public function getMaximumPlayers(): int {
        return match ($this->type) {
            self::TYPE_SOLO => 1,
            self::TYPE_DUO => 2,
            self::TYPE_TRIO => 3,
            self::TYPE_SQUAD => 4,
            default => 0
        };
    }
}