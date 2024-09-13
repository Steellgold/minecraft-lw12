<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class DurabilityComponent implements ItemComponent {

    /** @var int */
    private int $maxDurability;

    /**
     * @param int $maxDurability
     */
    public function __construct(int $maxDurability) {
        $this->maxDurability = $maxDurability;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:durability";
    }

    /**
     * @return int[]
     */
    public function getValue(): array {
        return [
            "max_durability" => $this->maxDurability
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}