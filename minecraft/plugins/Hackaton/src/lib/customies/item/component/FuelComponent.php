<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class FuelComponent implements ItemComponent {

    /** @var float  */
	private float $duration;

    /**
     * @param float $duration
     */
	public function __construct(float $duration) {
		$this->duration = $duration;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "minecraft:fuel";
	}

    /**
     * @return float[]
     */
	public function getValue(): array {
		return [
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