<?php

namespace hackaton\game;

use pocketmine\math\Vector3;

class SpawnPoint {

    /** @var bool */
    private bool $used = false;

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function __construct(
        private readonly int $x,
        private readonly int $y,
        private readonly int $z
    ) {
    }

    /**
     * @return int
     */
    public function getX(): int {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ(): int {
        return $this->z;
    }

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

    /**
     * @return Vector3
     */
    public function toVector3(): Vector3 {
        return new Vector3($this->x, $this->y, $this->z);
    }
}