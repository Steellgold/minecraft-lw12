<?php
declare(strict_types=1);

namespace hackaton\lib\customies\item\component;

use pocketmine\block\Block;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use function array_map;
use function implode;

final class DiggerComponent implements ItemComponent {

    /** @var array */
    private array $destroySpeeds;

    /**
     * @return string
     */
    public function getName(): string {
        return "minecraft:digger";
    }

    /**
     * @return array[]
     */
    public function getValue(): array {
        return [
            "destroy_speeds" => $this->destroySpeeds
        ];
    }

    /**
     * @return bool
     */
    public function isProperty(): bool {
        return false;
    }

    /**
     * @param int $speed
     * @param Block ...$blocks
     * @return $this
     */
    public function withBlocks(int $speed, Block ...$blocks): DiggerComponent {
        foreach ($blocks as $block) {
            $this->destroySpeeds[] = [
                "block" => [
                    "name" => GlobalBlockStateHandlers::getSerializer()->serialize($block->getStateId())->getName()
                ],
                "speed" => $speed
            ];
        }
        return $this;
    }

    /**
     * @param int $speed
     * @param string ...$tags
     * @return $this
     */
    public function withTags(int $speed, string ...$tags): DiggerComponent {
        $query = implode(",", array_map(fn($tag) => "'" . $tag . "'", $tags));
        $this->destroySpeeds[] = [
            "block" => [
                "tags" => "query.any_tag(" . $query . ")"
            ],
            "speed" => $speed
        ];
        return $this;
    }
}