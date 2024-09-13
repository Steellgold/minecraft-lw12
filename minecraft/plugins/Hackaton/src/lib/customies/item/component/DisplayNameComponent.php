<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class DisplayNameComponent implements ItemComponent {

    /** @var string  */
	private string $name;

    /**
     * @param string $name
     */
	public function __construct(string $name) {
		$this->name = $name;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "minecraft:display_name";
	}

    /**
     * @return string[]
     */
	public function getValue(): array {
		return [
			"value" => $this->name
		];
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return false;
	}
}