<?php

namespace hackaton\item;

use hackaton\form\lobby\ChoseGameForm;
use hackaton\GAPlayer;
use hackaton\lib\customies\item\CreativeInventoryInfo;
use hackaton\lib\customies\item\ItemComponents;
use hackaton\lib\customies\item\ItemComponentsTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class GameSelector extends Item implements ItemComponents {
    use ItemComponentsTrait;

    /**
     * @param ItemIdentifier $identifier
     * @param string $name
     */
    public function __construct(ItemIdentifier $identifier, string $name = "Unknown") {
        parent::__construct($identifier, $name);

        $this->initComponent("game_selector", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS));
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @param array $returnedItems
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult {
        /** @var GAPlayer $player */
        new ChoseGameForm($player);
        return ItemUseResult::SUCCESS();
    }
}