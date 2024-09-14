<?php

namespace hackaton;

use hackaton\game\Game;
use hackaton\manager\GameManager;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class GAPlayer extends Player {

    /** @var Game|null */
    private Game|null $game = null;

    /**
     * @return Game|null
     */
    public function getGame(): ?Game {
        return $this->game;
    }

    /**
     * @param Config $config
     * @return void
     */
    public function joinGame(Config $config): void {
        $games = GameManager::getInstance()->getGames();

        $selectedGames = array_filter($games, function ($game) use ($config) {
            return $game->getId() === (string)$config->get("id") && $game->isJoinable();
        });

        $game = array_values($selectedGames)[0] ?? null;

        if (is_null($game)) {
            GameManager::getInstance()->createGame($config)->onCompletion(function (Game $game) {
                $success = $game->join($this);
                if ($success) {
                    $this->game = $game;
                    return;
                }

                $this->sendMessage(Loader::PREFIX . "Failed to join the game. Please try again later.");
            }, fn() => null);
            return;
        }

        $success = $game->join($this);
        if ($success) {
            $this->game = $game;
            return;
        }

        $this->sendMessage(Loader::PREFIX . "Failed to join the game. Please try again later.");
    }
}