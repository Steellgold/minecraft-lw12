<?php
declare(strict_types=1);

namespace hackaton\lib\customies\block\permutations;

use hackaton\lib\customies\util\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

final class BlockProperty {

    /**
     * @param string $name
     * @param array $values
     */
	public function __construct(private readonly string $name, private readonly array $values) { }

    /**
     * Returns the name of the block property provided in the constructor.
     * @return string
     */
	public function getName(): string {
		return $this->name;
	}

    /**
     * Returns the array of possible values of the block property provided in the constructor.
     * @return array
     */
	public function getValues(): array {
		return $this->values;
	}

    /**
     * Returns the block property in the correct NBT format supported by the client.
     * @return CompoundTag
     */
	public function toNBT(): CompoundTag {
		$values = array_map(static fn($value) => NBT::getTagType($value), $this->values);
		return CompoundTag::create()
			->setString("name", $this->name)
			->setTag("enum", new ListTag($values));
	}
}