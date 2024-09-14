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
     * @param string $color
     */
    public function __construct(
        private readonly int $type,
        private readonly string $name,
        private readonly string $color
    ) { }

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
     * @return string
     */
    public function getColor(): string {
        return $this->color;
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
        $this->players[$player->getUniqueId()->toString()] = $player;
    }

    /**
     * @param GAPlayer $player
     * @return bool
     */
    public function removePlayer(GAPlayer $player): bool {
        $uuid = $player->getUniqueId()->toString();
        $player = $this->players[$uuid] ?? null;

        if (is_null($player)) return false;

        unset($this->players[$uuid]);
        return true;
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