<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class KnockbackResistanceComponent implements ItemComponent {

    /** @var float */
    private float $protection;

    /**
     * @param float $protection
     */
    public function __construct(float $protection) {
        $this->protection = $protection;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:knockback_resistance";
    }

    /**
     * @return float[]
     */
    public function getValue(): array {
        return [
            "protection" => $this->protection
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }
}