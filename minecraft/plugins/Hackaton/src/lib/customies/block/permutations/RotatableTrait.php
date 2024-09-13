<?php
declare(strict_types=1);

namespace hackaton\lib\customies\block\permutations;

use pocketmine\block\Block;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

trait RotatableTrait {
	use HorizontalFacingTrait;

	/**
	 * @return BlockProperty[]
	 */
	public function getBlockProperties(): array {
		return [
			new BlockProperty("customies:rotation", [2, 3, 4, 5]),
		];
	}

	/**
	 * @return Permutation[]
	 */
	public function getPermutations(): array {
		return [
			(new Permutation("q.block_property('customies:rotation') == 2"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 0)
					->setInt("RZ", 0)
					->setFloat("SX", 1.0)
					->setFloat("SY", 1.0)
					->setFloat("SZ", 1.0)
					->setFloat("TX", 0.0)
					->setFloat("TY", 0.0)
					->setFloat("TZ", 0.0)),
			(new Permutation("q.block_property('customies:rotation') == 3"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 2)
					->setInt("RZ", 0)
					->setFloat("SX", 1.0)
					->setFloat("SY", 1.0)
					->setFloat("SZ", 1.0)
					->setFloat("TX", 0.0)
					->setFloat("TY", 0.0)
					->setFloat("TZ", 0.0)),
			(new Permutation("q.block_property('customies:rotation') == 4"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 1)
					->setInt("RZ", 0)
					->setFloat("SX", 1.0)
					->setFloat("SY", 1.0)
					->setFloat("SZ", 1.0)
					->setFloat("TX", 0.0)
					->setFloat("TY", 0.0)
					->setFloat("TZ", 0.0)),
			(new Permutation("q.block_property('customies:rotation') == 5"))
				->withComponent("minecraft:transformation", CompoundTag::create()
					->setInt("RX", 0)
					->setInt("RY", 3)
					->setInt("RZ", 0)
					->setFloat("SX", 1.0)
					->setFloat("SY", 1.0)
					->setFloat("SZ", 1.0)
					->setFloat("TX", 0.0)
					->setFloat("TY", 0.0)
					->setFloat("TZ", 0.0)),
		];
	}

    /**
     * @return array
     */
	public function getCurrentBlockProperties(): array {
		return [$this->facing];
	}

    /**
     * @return int
     */
	protected function writeStateToMeta(): int {
		return Permutations::toMeta($this);
	}

    /**
     * @param int $id
     * @param int $stateMeta
     * @return void
     */
	public function readStateFromData(int $id, int $stateMeta): void {
		$blockProperties = Permutations::fromMeta($this, $stateMeta);
		$this->facing = $blockProperties[0] ?? Facing::NORTH;
	}

    /**
     * @return int
     */
	public function getStateBitmask(): int {
		return Permutations::getStateBitmask($this);
	}

    /**
     * @param BlockTransaction $tx
     * @param Item $item
     * @param Block $blockReplace
     * @param Block $blockClicked
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     * @return bool
     */
	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool {
		if($player !== null) {
			$this->facing = $player->getHorizontalFacing();
		}
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

    /**
     * @param BlockStateWriter $out
     * @return void
     */
	public function serializeState(BlockStateWriter $out): void {
		$out->writeInt("customies:rotation", $this->facing);
	}

    /**
     * @param BlockStateReader $in
     * @return void
     */
	public function deserializeState(BlockStateReader $in): void {
		$this->facing = $in->readInt("customies:rotation");
	}
}