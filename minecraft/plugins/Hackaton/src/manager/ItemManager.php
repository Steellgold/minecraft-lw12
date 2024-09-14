<?php

namespace hackaton\manager;

use hackaton\item\GameSelector;
use hackaton\item\LaserGun;
use hackaton\lib\customies\item\CustomiesItemFactory;
use pocketmine\utils\SingletonTrait;

class ItemManager {
    use SingletonTrait;

    public function initialize(): void {
        CustomiesItemFactory::getInstance()->registerItem(GameSelector::class, "hackaton:game_selector");
        CustomiesItemFactory::getInstance()->registerItem(LaserGun::class, "hackaton:laser_gun");
    }
}