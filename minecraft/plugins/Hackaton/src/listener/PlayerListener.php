<?php

namespace hackaton\listener;

use hackaton\game\Game;
use hackaton\player\GAPlayer;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
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
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        /** @var GAPlayer $player */
        $player = $event->getPlayer();
        $game = $player->getGame();

        if (is_null($game)) return;

        if ($game->getMode() === Game::MODE_WAITING ||$game->getMode() === Game::MODE_STARTING) {
            $event->cancel();
            return;
        }
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onPlayerChat(PlayerChatEvent $event): void {
        /** @var GAPlayer $player */
        $player = $event->getPlayer();
        $game = $player->getGame();

        $event->setFormatter($player->getChatFormatter());

        if (is_null($game)) return;

        $event->cancel();
        $game->broadcastPlayerMessage($event->getFormatter()->format($event->getPlayer()->getName(), $event->getMessage()));
    }
}