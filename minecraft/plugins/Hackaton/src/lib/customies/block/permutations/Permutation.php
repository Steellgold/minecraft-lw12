<?php
declare(strict_types=1);

namespace hackaton\lib\customies\block\permutations;

use hackaton\lib\customies\util\NBT;
use pocketmine\nbt\tag\CompoundTag;

final class Permutation {

    /** @var CompoundTag  */
	private CompoundTag $components;

    /**
     * @param string $condition
     */
	public function __construct(private readonly string $condition) {
		$this->components = CompoundTag::create();
	}

    /**
     * Returns the permutation with the provided component added to the current list of components.
     * @param string $component
     * @param mixed $value
     * @return $this
     */
	public function withComponent(string $component, mixed $value) : self {
		$this->components->setTag($component, NBT::getTagType($value));
		return $this;
	}

    /**
     * Returns the permutation in the correct NBT format supported by the client.
     * @return CompoundTag
     */
	public function toNBT(): CompoundTag {
		return CompoundTag::create()
			->setString("condition", $this->condition)
			->setTag("components", $this->components);
	}
}