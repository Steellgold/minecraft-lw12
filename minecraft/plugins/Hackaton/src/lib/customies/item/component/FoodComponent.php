<?php
declare(strict_types=1);

namespace wapy\libs\customiesdevs\customies\item\component;

final class FoodComponent implements ItemComponent {

    /** @var bool */
    private bool $canAlwaysEat;

    /**
     * @param bool $canAlwaysEat
     */
    public function __construct(bool $canAlwaysEat = false) {
        $this->canAlwaysEat = $canAlwaysEat;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:food";
    }

    /**
     * @return bool[]
     */
    public function getValue(): array {
        return [
            "can_always_eat" => $this->canAlwaysEat
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}