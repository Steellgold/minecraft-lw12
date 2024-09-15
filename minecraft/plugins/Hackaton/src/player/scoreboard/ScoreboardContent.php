<?php

namespace hackaton\player\scoreboard;

class ScoreboardContent {

    /**
     * @param int $column
     * @param int $line
     * @param string $text
     */
    public function __construct(
        private readonly int $column,
        private readonly int $line,
        private readonly string $text
    ) {
    }

    /**
     * @return int
     */
    public function getColumn(): int {
        return $this->column;
    }

    /**
     * @return int
     */
    public function getLine(): int {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }
}