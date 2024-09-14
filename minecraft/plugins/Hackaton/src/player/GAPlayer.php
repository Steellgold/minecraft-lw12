<?php

namespace hackaton\player;

use hackaton\game\Game;
use hackaton\Loader;
use hackaton\manager\GameManager;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\utils\Config;

class GAPlayer extends Player {

    /** @var Game|null */
    private Game|null $game = null;

    /** @var PlayerChatFormatter */
    private PlayerChatFormatter $chatFormatter;

    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag) {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        $this->chatFormatter = new PlayerChatFormatter($this);
    }

    /**
     * @return Game|null
     */
    public function getGame(): ?Game {
        return $this->game;
    }

    /**
     * @return PlayerChatFormatter
     */
    public function getChatFormatter(): PlayerChatFormatter {
        return $this->chatFormatter;
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