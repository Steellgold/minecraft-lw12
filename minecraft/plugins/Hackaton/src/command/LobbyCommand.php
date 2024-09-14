<?php

namespace hackaton\command;

use hackaton\lib\customies\item\CustomiesItemFactory;
use hackaton\Loader;
use hackaton\player\GAPlayer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;

class LobbyCommand extends Command {

    public function __construct() {
        parent::__construct("lobby", "Teleport to the lobby");
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof GAPlayer) return;

        $game = $sender->getGame();
        if (!is_null($game)) $game->quit($sender);

        $sender->teleport(Loader::getInstance()->getLobbySpawn());

        $sender->clearInventories();
        $sender->getInventory()->setItem(4, CustomiesItemFactory::getInstance()->get("hackaton:game_selector"));
    }
}