<?php

namespace hackaton\player;

use hackaton\game\Game;
use hackaton\Loader;
use hackaton\manager\GameManager;
use hackaton\player\scoreboard\Scoreboard;
use hackaton\task\async\PutAsyncTask;
use JsonException;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\sound\Sound;

class GAPlayer extends Player {

    /** @var Scoreboard */
    private Scoreboard $scoreboard;

    /** @var PlayerSession|null */
    private ?PlayerSession $session = null;

    /** @var Skin */
    private Skin $defaultSkin;

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
        $this->defaultSkin = $this->getSkin();
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
     * @return PlayerSession|null
     */
    public function getSession(): ?PlayerSession {
        return $this->session;
    }

    /**
     * @param PlayerSession|null $session
     */
    public function setSession(?PlayerSession $session): void {
        $this->session = $session;
    }

    /**
     * @return Skin
     */
    public function getDefaultSkin(): Skin {
        return $this->defaultSkin;
    }

    /**
     * @return void
     */
    public function quitServer(): void {
        new PutAsyncTask("/" . $this->getName() . "/status", [], fn() => null);
    }

    /**
     * @param Config $config
     * @return void
     * @throws JsonException
     */
    public function joinGame(Config $config): void {
        $games = GameManager::getInstance()->getGames();

        $selectedGames = array_filter($games, function ($game) use ($config) {
            return $game->getConfigId() === (string)$config->get("id") && $game->isJoinable();
        });

        $game = array_values($selectedGames)[0] ?? null;

        if (is_null($game)) {
            GameManager::getInstance()->createGame($config)->onCompletion(function (Game $game) {
                $success = $game->join($this);
                if ($success) return;

                $this->sendMessage(Loader::PREFIX . "Failed to join the game. Please try again later.");
            }, fn() => null);
            return;
        }

        $success = $game->join($this);
        if ($success) return;

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