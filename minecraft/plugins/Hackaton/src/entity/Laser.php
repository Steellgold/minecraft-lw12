<?php

namespace hackaton\entity;

use hackaton\player\GAPlayer;
use hackaton\task\RespawnTask;
use pocketmine\color\Color;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\GameMode;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\SnowballPoofParticle;
use pocketmine\world\sound\ExplodeSound;

class Laser extends Throwable {

    /** @var Color */
    private Color $color;

    /**
     * @param Location $location
     * @param Entity|null $shootingEntity
     * @param CompoundTag|null $nbt
     */
    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null) {
        $this->color = new Color(255, 0, 0);
        parent::__construct($location, $shootingEntity, $nbt);
    }

    /**
     * @return string
     */
    public static function getNetworkTypeId(): string {
        return "hackaton:laser";
    }

    /**
     * @return float
     */
    protected function getInitialGravity(): float {
        return 0;
    }

    /**
     * @param float $dx
     * @param float $dy
     * @param float $dz
     * @return void
     */
    protected function move(float $dx, float $dy, float $dz): void {
        parent::move($dx, $dy, $dz);

        $world = $this->getWorld();
        $world->addParticle($this->location, new DustParticle($this->color));
    }

    /**
     * @param ProjectileHitEvent $event
     * @return void
     */
    protected function onHit(ProjectileHitEvent $event): void {
        $world = $this->getWorld();

        if (!$event instanceof ProjectileHitEntityEvent) return;

        $hitEntity = $event->getEntityHit();
        if (!$hitEntity instanceof GAPlayer) return;

        // Check if the player is int the same game
        $shootingEntity = $this->getOwningEntity();
        if (!$shootingEntity instanceof GAPlayer) return;

        $session = $shootingEntity->getSession();
        if (is_null($session)) return;

        $team = $session->getGame()->getTeamByPlayer($shootingEntity);
        if (is_null($team)) return;

        $hitTeam = $session->getGame()->getTeamByPlayer($hitEntity);
        if (is_null($hitTeam)) return;

        // Check if the player is in the same team
        if ($team->getName() === $hitTeam->getName()) return;

        $location = clone $hitEntity->location->add(0, 1, 0);

        // Add particle to create a sphere
        for ($i = 0; $i < 180; $i += 10) {
            for ($j = 0; $j < 360; $j += 10) {
                $x = $location->x + 1.5 * sin(deg2rad($i)) * cos(deg2rad($j));
                $y = $location->y + 1.5 * sin(deg2rad($i)) * sin(deg2rad($j));
                $z = $location->z + 1.5 * cos(deg2rad($i));
                $world->addParticle(new Vector3($x, $y, $z), new DustParticle($this->color));
            }
        }

        $hitEntity->clearInventories();
        $hitEntity->setGamemode(GameMode::SPECTATOR());
        $hitEntity->sendTitle("§4» §cYou are dead §4«");
        $hitEntity->sendSound(new ExplodeSound());
        new RespawnTask($session->getGame(), $hitEntity);

        $hitEntity->getSession()?->addDeath();
        $session->addKill();

        NetworkBroadcastUtils::broadcastPackets([$shootingEntity], [
            PlaySoundPacket::create(
                "random.orb",
                $shootingEntity->location->getX(),
                $shootingEntity->location->getY(),
                $shootingEntity->location->getZ(),
                1,
                1
            )
        ]);
    }

    /**
     * @param Color $color
     */
    public function setColor(Color $color): void {
        $this->color = $color;
    }
}