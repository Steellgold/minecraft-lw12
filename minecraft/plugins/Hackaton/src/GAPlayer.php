<?php

namespace hackaton;

use hackaton\game\Game;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class GAPlayer extends Player {

    /**
     * @param Config $config
     * @return void
     */
    public function joinGame(Config $config): void {
        $games = Loader::getInstance()->getGames();

        $selectedGames = array_filter($games, function($game) use ($config) {
            return $game->getId() === (string)$config->get("id") && $game->isJoinable();
        });

        $game = array_values($selectedGames)[0] ?? null;

        if (is_null($game)) $game = Game::create($config);

        $result = $game->join($this);
        if (!$result) {
            $this->sendMessage(Loader::PREFIX . "Failed to join the game");
            return;
        }

        $this->sendMessage(Loader::PREFIX . "You have joined the game");
    }
}