<?php

namespace hackaton\game;

use pocketmine\math\Vector3;

class SpawnPoint extends Vector3 {

    /** @var bool */
    private bool $used = false;

    /**
     * @return bool
     */
    public function isUsed(): bool {
        return $this->used;
    }

    /**
     * @param bool $used
     */
    public function setUsed(bool $used): void {
        $this->used = $used;
    }
}