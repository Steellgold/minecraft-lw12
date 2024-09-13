<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class ChargeableComponent implements ItemComponent {

    /** @var float  */
	private float $movementModifier;

    /**
     * @param float $movementModifier
     */
	public function __construct(float $movementModifier) {
		$this->movementModifier = $movementModifier;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "minecraft:chargeable";
	}

    /**
     * @return float[]
     */
	public function getValue(): array {
		return [
			"movement_modifier" => $this->movementModifier
		];
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return false;
	}
}