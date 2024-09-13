<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

final class ProjectileComponent implements ItemComponent {

    /** @var string  */
	private string $projectileEntity;

    /**
     * @param string $projectileEntity
     */
	public function __construct(string $projectileEntity) {
		$this->projectileEntity = $projectileEntity;
	}

    /**
     * @return string
     */
	public function getName(): string {
		return "minecraft:projectile";
	}

    /**
     * @return string[]
     */
	public function getValue(): array {
		return [
			"projectile_entity" => $this->projectileEntity
		];
	}

    /**
     * @return bool
     */
	public function isProperty(): bool {
		return false;
	}
}