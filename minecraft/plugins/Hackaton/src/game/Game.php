<?php

namespace hackaton\game;

use hackaton\GAPlayer;
use hackaton\task\WaitingGameTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\World;

class Game {

    /** @var int */
    public const MODE_WAITING = 0;

    /** @var int */
    public const MODE_STARTING = 1;

    /** @var int */
    public const MODE_RUNNING = 2;

    /** @var int */
    public const MODE_FINISHED = 3;

    /** @var int */
    private int $mode = self::MODE_WAITING;

    /** @var Team[] */
    private array $teams = [];

    /**
     * @param string $id
     * @param string $name
     * @param string $description
     * @param int $minPlayers
     * @param int $maxPlayers
     * @param int $duration
     * @param array $teamNames
     * @param array $spawnPoints
     * @param World $world
     */
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $description,
        private readonly int $minPlayers,
        private readonly int $maxPlayers,
        private readonly int $duration,
        array $teamNames,
        private readonly array $spawnPoints,
        private readonly World $world
    ) {
        foreach ($teamNames as $teamName) {
            $this->teams[] = new Team(Team::TYPE_SOLO, $teamName);
        }

        new WaitingGameTask($this);
    }

    /**
     * @param Config $config
     * @return Game
     */
    public static function create(Config $config): Game {
        return new Game(
            $config->get("id"),
            $config->get("name"),
            $config->get("description"),
            $config->get("min_players"),
            $config->get("max_players"),
            $config->get("duration"),
            $config->get("teams"),
            array_map(function ($location) {
                return new SpawnPoint($location["x"], $location["y"], $location["z"]);
            }, $config->get("spawn_points")),
            Server::getInstance()->getWorldManager()->getWorldByName($config->getNested("arena.name"))
        );
    }

    /**
     * @return int
     */
    public function getMode(): int {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function setMode(int $mode): void {
        $this->mode = $mode;
    }

    /**
     * @return array
     */
    public function getTeams(): array {
        return $this->teams;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getMinPlayers(): int {
        return $this->minPlayers;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    /**
     * @return int
     */
    public function getPlayersCount(): int {
        $count = 0;
        foreach ($this->teams as $team) {
            $count += count($team->getPlayers());
        }
        return $count;
    }

    /**
     * @return int
     */
    public function getDuration(): int {
        return $this->duration;
    }

    /**
     * @return World
     */
    public function getWorld(): World {
        return $this->world;
    }

    /**
     * @return SpawnPoint[]
     */
    public function getSpawnPoints(): array {
        return array_filter($this->spawnPoints, function(SpawnPoint $spawnPoint) {
            return !$spawnPoint->isUsed();
        });
    }

    /**
     * @return Team|null
     */
    public function getSmallestTeam(): ?Team {
        $smallest = null;
        foreach ($this->teams as $team) {
            if ($smallest === null || count($team->getPlayers()) < count($smallest->getPlayers())) {
                $smallest = $team;
            }
        }
        return $smallest;
    }

    /**
     * @return bool
     */
    public function isJoinable(): bool {
        return ($this->mode === self::MODE_WAITING || $this->mode === self::MODE_STARTING) && $this->getPlayersCount() < $this->getMaxPlayers();
    }

    /**
     * @param GAPlayer $player
     * @return bool
     */
    public function join(GAPlayer $player): bool {
        if (!$this->isJoinable()) return false;

        $team = $this->getSmallestTeam();
        $team->addPlayer($player);

        $spawnPoint = $this->getSpawnPoints()[0];
        if (is_null($spawnPoint)) return false;

        $spawnPoint->setUsed(true);

        $player->teleport(new Position($spawnPoint->getX(), $spawnPoint->getY(), $spawnPoint->getZ(), $this->getWorld()));

        return true;
    }

    /**
     * @param string $message
     * @return void
     */
    public function sendMessages(string $message): void {
        foreach ($this->teams as $team) {
            foreach ($team->getPlayers() as $player) $player->sendMessage($message);
        }
    }
}