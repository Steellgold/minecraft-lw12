<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class IconComponent implements ItemComponent {

    /** @var string  */
	private string $texture;

    /**
     * @param string $texture
     */
	public function __construct(string $texture) {
		$this->texture = $texture;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "minecraft:icon";
	}

    /**
     * @return array[]
     */
	public function getValue(): array {
		return [
			"textures" => [
				"default" => $this->texture
			]
		];
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return true;
	}
}
