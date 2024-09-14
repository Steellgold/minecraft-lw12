<?php

namespace hackaton\game;

use hackaton\lib\customies\item\CustomiesItemFactory;
use hackaton\player\GAPlayer;
use hackaton\task\async\CopyWorldAsync;
use hackaton\task\WaitingGameTask;
use pocketmine\block\VanillaBlocks;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\sound\Sound;
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
     * @param string $prefix
     * @param string $description
     * @param int $minPlayers
     * @param int $maxPlayers
     * @param int $duration
     * @param array $teams
     * @param array $spawnPoints
     * @param World $world
     */
    public function __construct(
        private readonly string $id,
        private readonly string $prefix,
        private readonly string $description,
        private readonly int $minPlayers,
        private readonly int $maxPlayers,
        private readonly int $duration,
        array $teams,
        private readonly array $spawnPoints,
        private readonly World $world
    ) {
        foreach ($teams as $team) {
            $this->teams[] = new Team(Team::TYPE_SOLO, $team["name"], $team["color"], $team["laser_color"]);
        }

        new WaitingGameTask($this);
    }

    /**
     * @param Config $config
     * @return Promise
     */
    public static function create(Config $config): Promise {
        $promiseResolver = new PromiseResolver();

        new CopyWorldAsync($config->get("id"), function (?string $worldName) use ($promiseResolver, $config) {
            if (is_null($worldName)) {
                $promiseResolver->resolve(null);
                return;
            }

            $promiseResolver->resolve(new Game(
                $config->get("id"),
                $config->get("prefix"),
                $config->get("description"),
                $config->get("min_players"),
                $config->get("max_players"),
                $config->get("duration"),
                $config->get("teams"),
                array_map(function ($location) {
                    return new SpawnPoint($location["x"], $location["y"], $location["z"]);
                }, $config->get("spawn_points")),
                Server::getInstance()->getWorldManager()->getWorldByName($worldName)
            ));
        });

        return $promiseResolver->getPromise();
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
    public function getPrefix(): string {
        return $this->prefix;
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
        return $this->spawnPoints;
    }

    /**
     * @param GAPlayer $player
     * @return SpawnPoint|null
     */
    public function getPlayerSpawnPoint(GAPlayer $player): ?SpawnPoint {
        foreach ($this->spawnPoints as $spawnPoint) {
            if ($spawnPoint->getPlayer() === $player) return $spawnPoint;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getUnusedSpawnPoints(): array {
        return array_filter($this->spawnPoints, function(SpawnPoint $spawnPoint) {
            return is_null($spawnPoint->getPlayer());
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

    public function getTeamByPlayer(GAPlayer $player): ?Team {
        foreach ($this->teams as $team) {
            if (in_array($player, $team->getPlayers())) {
                return $team;
            }
        }
        return null;
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
        if (is_null($team)) return false;

        $team->addPlayer($player);

        $spawnPoints = $this->getUnusedSpawnPoints();
        if (empty($spawnPoints)) return false;

        $spawnPoint = $spawnPoints[array_rand($spawnPoints)];
        if (is_null($spawnPoint)) return false;

        $spawnPoint->setPlayer($player);

        $player->teleport(new Position($spawnPoint->getX(), $spawnPoint->getY(), $spawnPoint->getZ(), $this->getWorld()));

        $this->broadcastMessage("§a{$player->getName()} joined the game (" . $this->getPlayersCount() . "/" . $this->getMaxPlayers() . ")", true);

        $player->clearInventories();

        return true;
    }

    /**
     * @param GAPlayer $player
     * @return void
     */
    public function quit(GAPlayer $player): void {
        foreach ($this->teams as $team) {
            $success = $team->removePlayer($player);
            if (!$success) continue;

            $spawnPoint = $this->getPlayerSpawnPoint($player);
            if (!is_null($spawnPoint)) $spawnPoint->setPlayer(null);

            $this->broadcastMessage("§c{$player->getName()} left the game (" . $this->getPlayersCount() . "/" . $this->getMaxPlayers() . ")", true);
        }
    }

    /**
     * @param string $message
     * @param bool $prefix
     * @return void
     */
    public function broadcastMessage(string $message, bool $prefix = false): void {
        foreach ($this->teams as $team) {
            foreach ($team->getPlayers() as $player) $player->sendMessage(($prefix ? $this->getPrefix() : "") . $message);
        }
    }

    /**
     * @param string $title
     * @param string $subtitle
     * @return void
     */
    public function broadcastTitle(string $title, string $subtitle = ""): void {
        foreach ($this->teams as $team) {
            foreach ($team->getPlayers() as $player) $player->sendTitle($title, $subtitle);
        }
    }

    /**
     * @param Sound $sound
     * @return void
     */
    public function broadcastSound(Sound $sound): void {
        foreach ($this->teams as $team) {
            foreach ($team->getPlayers() as $player) $player->sendSound($sound);
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function broadcastPlayerMessage(string $message): void {
        foreach ($this->teams as $team) {
            foreach ($team->getPlayers() as $p) $p->sendMessage($message);
        }
    }

    /**
     * @return void
     */
    public function start(): void {
        foreach ($this->getSpawnPoints() as $spawnPoint) {
            $this->getWorld()->setBlock($spawnPoint->add(0, -1, 0), VanillaBlocks::AIR());
        }

        foreach ($this->teams as $team) {
            foreach ($team->getPlayers() as $player) {
                $player->getInventory()->setItem(0, CustomiesItemFactory::getInstance()->get("hackaton:laser_gun"));
            }
        }
    }
}