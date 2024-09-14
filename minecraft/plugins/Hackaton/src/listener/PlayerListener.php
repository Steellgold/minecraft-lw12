<?php

namespace hackaton\listener;

use hackaton\game\Game;
use hackaton\lib\customies\item\CustomiesItemFactory;
use hackaton\Loader;
use hackaton\player\formatter\BasicChatFormatter;
use hackaton\player\formatter\GameChatFormatter;
use hackaton\player\GAPlayer;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Server;
use pocketmine\world\Position;

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
        $event->cancel();
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onPlayerChat(PlayerChatEvent $event): void {
        /** @var GAPlayer $player */
        $player = $event->getPlayer();
        $recipients = $event->getRecipients();
        $game = $player->getGame();

        $event->setFormatter(new BasicChatFormatter());

        if (!is_null($game)) $event->setFormatter(new GameChatFormatter($player));

        foreach ($recipients as $key => $recipient) {
            if ($recipient instanceof GAPlayer) {
                if ($recipient->getGame() !== $game) {
                    unset($recipients[$key]);
                }
            }
        }

        $event->setRecipients($recipients);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $event->setQuitMessage("");

        /** @var GAPlayer $player */
        $player = $event->getPlayer();
        $game = $player->getGame();

        if (is_null($game)) return;

        $game->quit($player);
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $event->setJoinMessage("");

        /** @var GAPlayer $player */
        $player = $event->getPlayer();

        $config = Loader::getInstance()->getConfig();

        $lobbyWorld = Server::getInstance()->getWorldManager()->getWorldByName($config->getNested("lobby.world"));
        if (is_null($lobbyWorld)) {
            $player->kick("An error occurred while trying to join the server. Please try again later.");
            return;
        }

        $position = new Position(
            $config->getNested("lobby.spawn.x"),
            $config->getNested("lobby.spawn.y"),
            $config->getNested("lobby.spawn.z"),
            $lobbyWorld
        );
        $player->teleport($position);

        $player->clearInventories();
        $player->getInventory()->setItem(4, CustomiesItemFactory::getInstance()->get("hackaton:game_selector"));
    }

    /**
     * @param PlayerRespawnEvent $event
     * @return void
     */
    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        /** @var GAPlayer $player */
        $player = $event->getPlayer();
        $game = $player->getGame();
        if (is_null($game)) return;

        $spawnPoint = $game->getPlayerSpawnPoint($player);
        if (is_null($spawnPoint)) return;

        $event->setRespawnPosition(new Position($spawnPoint->x, $spawnPoint->y, $spawnPoint->z, $game->getWorld()));
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $event->setDeathMessage("");

        /** @var GAPlayer $player */
        $player = $event->getPlayer();
        $game = $player->getGame();
        if (is_null($game)) return;

        $event->setKeepInventory(true);
    }
}