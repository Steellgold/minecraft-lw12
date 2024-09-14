<?php

namespace hackaton\item;

use hackaton\entity\Laser;
use hackaton\lib\customies\item\CreativeInventoryInfo;
use hackaton\lib\customies\item\ItemComponents;
use hackaton\lib\customies\item\ItemComponentsTrait;
use hackaton\player\GAPlayer;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class LaserGun extends ProjectileItem implements ItemComponents {
    use ItemComponentsTrait;

    /**
     * @param ItemIdentifier $identifier
     * @param string $name
     */
    public function __construct(ItemIdentifier $identifier, string $name = "Unknown") {
        parent::__construct($identifier, $name);

        $this->initComponent("laser_gun", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));
        $this->setupRenderOffsets(40, 32, true);
    }

    /**
     * @return float
     */
    public function getThrowForce(): float {
        return 3;
    }

    /**
     * @param Location $location
     * @param Player $thrower
     * @return Throwable
     */
    protected function createEntity(Location $location, Player $thrower): Throwable {
        $laser = new Laser($location, $thrower);

        if ($thrower instanceof GAPlayer) {
            $game = $thrower->getGame();
            if (!is_null($game)) {
                $team = $game->getTeamByPlayer($thrower);
                if (!is_null($team)) {
                    $laser->setColor($team->getLaserColor());
                }
            }
        }

        return $laser;
    }

    /**
     * @return int
     */
    public function getMaxStackSize(): int {
        return 1;
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @param array $returnedItems
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult{
        $location = $player->getLocation();

        $projectile = $this->createEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player);
        $projectile->setMotion($directionVector->multiply($this->getThrowForce()));

        $projectileEv = new ProjectileLaunchEvent($projectile);
        $projectileEv->call();
        if($projectileEv->isCancelled()){
            $projectile->flagForDespawn();
            return ItemUseResult::FAIL;
        }

        $projectile->spawnToAll();

        return ItemUseResult::SUCCESS;
    }
}