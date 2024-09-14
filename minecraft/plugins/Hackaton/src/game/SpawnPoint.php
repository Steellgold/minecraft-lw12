<?php

namespace hackaton\game;

use hackaton\player\GAPlayer;
use pocketmine\math\Vector3;

class SpawnPoint extends Vector3 {

    /** @var GAPlayer|null */
    private ?GAPlayer $player = null;

    /**
     * @return GAPlayer|null
     */
    public function getPlayer(): ?GAPlayer {
        return $this->player;
    }

    /**
     * @param GAPlayer|null $player
     */
    public function setPlayer(?GAPlayer $player): void {
        $this->player = $player;
    }
}