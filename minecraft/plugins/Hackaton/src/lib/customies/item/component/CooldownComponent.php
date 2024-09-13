<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class CooldownComponent implements ItemComponent {

    /** @var string */
    private string $category;

    /** @var float */
    private float $duration;

    /**
     * @param string $category
     * @param float $duration
     */
    public function __construct(string $category, float $duration) {
        $this->category = $category;
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:cooldown";
    }

    /**
     * @return array
     */
    public function getValue(): array {
        return [
            "category" => $this->category,
            "duration" => $this->duration
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}