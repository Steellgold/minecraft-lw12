<?php

namespace hackaton\manager;

use hackaton\entity\Laser;
use hackaton\lib\customies\entity\CustomiesEntityFactory;
use pocketmine\utils\SingletonTrait;

class EntityManager {
    use SingletonTrait;

    public function initialize(): void {
        CustomiesEntityFactory::getInstance()->registerEntity(Laser::class, "wapy:laser");
    }
}