<?php

namespace hackaton\game;

class Team {

    /** @var int */
    public const TYPE_SOLO = 0;

    /** @var int */
    public const TYPE_DUO = 1;

    /** @var int */
    public const TYPE_TRIO = 2;

    /** @var int */
    public const TYPE_SQUAD = 3;

    /**
     * @param int $type
     */
    public function __construct(private readonly int $type) { }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }
}