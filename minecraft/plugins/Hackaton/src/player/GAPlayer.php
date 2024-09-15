<?php

namespace hackaton\player;

use hackaton\game\Game;
use hackaton\Loader;
use hackaton\manager\GameManager;
use hackaton\player\scoreboard\Scoreboard;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\sound\Sound;

class GAPlayer extends Player {

    /** @var Game|null */
    private Game|null $game = null;

    /** @var Scoreboard */
    private Scoreboard $scoreboard;

    /**
     * @param Server $server
     * @param NetworkSession $session
     * @param PlayerInfo $playerInfo
     * @param bool $authenticated
     * @param Location $spawnLocation
     * @param CompoundTag|null $namedtag
     */
    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag) {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        Scoreboard::create($this);
    }

    /**
     * @return Scoreboard
     */
    public function getScoreboard(): Scoreboard {
        return $this->scoreboard;
    }

    /**
     * @param Scoreboard $scoreboard
     */
    public function setScoreboard(Scoreboard $scoreboard): void {
        $this->scoreboard = $scoreboard;
    }

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

    /**
     * @param Sound $sound
     * @return void
     */
    public function sendSound(Sound $sound): void {
        $this->getWorld()->addSound($this->getPosition(), $sound, [$this]);
    }

    /**
     * @return void
     */
    public function clearInventories(): void {
        $this->getInventory()->clearAll();
        $this->getArmorInventory()->clearAll();
        $this->getCursorInventory()->clearAll();
        $this->getOffHandInventory()->clearAll();
        $this->getEnderInventory()->clearAll();
    }
}