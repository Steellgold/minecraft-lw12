<?php

namespace hackaton\listener;

use hackaton\GAPlayer;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerMoveEvent;

class PlayerListener extends GameListener {

    /**
     * @param PlayerCreationEvent $event
     * @return void
     */
    public function onPlayerCreation(PlayerCreationEvent $event): void {
        $event->setPlayerClass(GAPlayer::class);
    }

    /**
     * @param PlayerMoveEvent $event
     * @return void
     */
    public function onPlayerMove(PlayerMoveEvent $event): void {
        $x = $event->getPlayer()->getPosition()->getX();
        $y = $event->getPlayer()->getPosition()->getY();
        $z = $event->getPlayer()->getPosition()->getZ();
        $event->getPlayer()->sendTip("X: $x Y: $y Z: $z");
    }
}