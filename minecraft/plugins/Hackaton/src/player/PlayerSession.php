<?php

namespace hackaton\player;

use hackaton\game\Game;
use pocketmine\Server;
use Ramsey\Uuid\UuidInterface;

class PlayerSession {

    /** @var int */
    private int $kills = 0;

    /** @var int */
    private int $deaths = 0;

    /**
     * @param UuidInterface $uuid
     * @param string $name
     * @param Game $game
     */
    public function __construct(private readonly UuidInterface $uuid, private readonly string $name, private readonly Game $game) {
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return GAPlayer|null
     */
    public function getPlayer(): ?GAPlayer {
        $player = Server::getInstance()->getPlayerByUUID($this->uuid);
        if ($player instanceof GAPlayer) return $player;

        return null;
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
    public function getKills(): int {
        return $this->kills;
    }

    /**
     * @return void
     */
    public function addKill(): void {
        $this->kills++;
    }

    /**
     * @return int
     */
    public function getDeaths(): int {
        return $this->deaths;
    }

    /**
     * @return void
     */
    public function addDeath(): void {
        $this->deaths++;
    }
}