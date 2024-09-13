<?php

namespace hackaton\listener;

use hackaton\GAPlayer;
use pocketmine\event\player\PlayerCreationEvent;

class PlayerListener extends GameListener {

    /**
     * @param PlayerCreationEvent $event
     * @return void
     */
    public function onPlayerCreation(PlayerCreationEvent $event): void {
        $event->setPlayerClass(GAPlayer::class);
    }
}