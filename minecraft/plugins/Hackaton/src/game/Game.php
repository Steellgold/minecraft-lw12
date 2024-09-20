<?php

namespace hackaton\game;

use hackaton\lib\customies\item\CustomiesItemFactory;
use hackaton\player\GAPlayer;
use hackaton\player\PlayerSession;
use hackaton\resource\Resource;
use hackaton\task\async\CopyWorldAsync;
use hackaton\task\async\PatchAsyncTask;
use hackaton\task\async\PostAsyncTask;
use hackaton\task\async\RequestError;
use hackaton\task\WaitingGameTask;
use JsonException;
use MongoDB\Driver\Session;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Skin;
use pocketmine\player\GameMode;
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

    /** @var bool */
    private bool $finish = false;

    /** @var null|string */
    private ?string $id = null;

    /**
     * @param string $configId
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
        private readonly string $configId,
        private readonly string $prefix,
        private readonly string $description,
        private readonly int    $minPlayers,
        private readonly int    $maxPlayers,
        private readonly int    $duration,
        array                   $teams,
        private readonly array  $spawnPoints,
        private readonly World  $world
    ) {
        foreach ($teams as $team) {
            $this->teams[] = new Team(Team::TYPE_SOLO, $team["name"], $team["color"], $team["laser_color"], $team["icon"]);
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
                (int)$config->get("min_players"),
                (int)$config->get("max_players"),
                (int)$config->get("game_duration"),
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
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfigId(): string {
        return $this->configId;
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
            $count += count($team->getSessions());
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
     * @param string $uuid
     * @return PlayerSession|null
     */
    public function getPlayerSession(string $uuid): ?PlayerSession {
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) {
                if ($session->getUuid()->toString() === $uuid) return $session;
            }
        }
        return null;
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
        return array_filter($this->spawnPoints, function (SpawnPoint $spawnPoint) {
            return is_null($spawnPoint->getPlayer());
        });
    }

    /**
     * @return Team|null
     */
    public function getSmallestTeam(): ?Team {
        $smallest = null;
        foreach ($this->teams as $team) {
            if ($smallest === null || count($team->getSessions()) < count($smallest->getSessions())) {
                $smallest = $team;
            }
        }
        return $smallest;
    }

    public function getTeamByPlayer(GAPlayer $player): ?Team {
        foreach ($this->teams as $team) {
            if (in_array($player->getSession(), $team->getSessions())) {
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
     * @return bool
     */
    public function isFinish(): bool {
        return $this->finish;
    }

    /**
     * @param bool $finish
     */
    public function setFinish(bool $finish): void {
        $this->finish = $finish;
    }

    /**
     * @param GAPlayer $player
     * @return bool
     * @throws JsonException
     */
    public function join(GAPlayer $player): bool {
        if (!$this->isJoinable()) return false;

        $team = $this->getSmallestTeam();
        if (is_null($team)) return false;

        $spawnPoints = $this->getUnusedSpawnPoints();
        if (empty($spawnPoints)) return false;

        $spawnPoint = $spawnPoints[array_rand($spawnPoints)];
        if (is_null($spawnPoint)) return false;

        $spawnPoint->setPlayer($player);

        $player->setSession(new PlayerSession($player->getUniqueId(), $player->getName(), $this));
        $team->addSession($player->getSession());

        $skinName = strtoupper($team->getName());
        $player->setSkin(new Skin("TEAM_{$skinName}", Resource::PNGtoBYTES($skinName)));
        $player->sendSkin();

        $player->teleport(new Position($spawnPoint->getX(), $spawnPoint->getY(), $spawnPoint->getZ(), $this->getWorld()));

        $this->broadcastMessage("§a§l» §r§7{$player->getName()} joined the game. §8[" . $this->getPlayersCount() . "/" . $this->getMaxPlayers() . "]");

        $player->clearInventories();

        return true;
    }

    /**
     * @param GAPlayer $player
     * @return void
     */
    public function quit(GAPlayer $player): void {
        foreach ($this->teams as $team) {
            if ($this->getMode() === self::MODE_WAITING || $this->getMode() === self::MODE_STARTING) {
                $success = $team->removeSession($player->getSession());
                if (!$success) continue;
            }

            $player->setSession(null);
            $spawnPoint = $this->getPlayerSpawnPoint($player);
            if (!is_null($spawnPoint)) $spawnPoint->setPlayer(null);

            $player->getScoreboard()->remove();

            $player->setSkin($player->getDefaultSkin());
            $player->sendSkin();

            $this->broadcastMessage("§c§l» §r§7{$player->getName()} left the game. §8[" . $this->getPlayersCount() . "/" . $this->getMaxPlayers() . "]");
        }
    }

    /**
     * @param string $message
     * @param bool $prefix
     * @return void
     */
    public function broadcastMessage(string $message, bool $prefix = false): void {
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) $session->getPlayer()?->sendMessage(($prefix ? $this->getPrefix() : "") . $message);
        }
    }

    /**
     * @param string $title
     * @param string $subtitle
     * @return void
     */
    public function broadcastTitle(string $title, string $subtitle = ""): void {
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) $session->getPlayer()?->sendTitle($title, $subtitle);
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function broadcastActionBarMessage(string $message): void {
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) $session->getPlayer()?->sendActionBarMessage($message);
        }
    }

    /**
     * @param Sound $sound
     * @return void
     */
    public function broadcastSound(Sound $sound): void {
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) $session->getPlayer()?->sendSound($sound);
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function broadcastPlayerMessage(string $message): void {
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) $session->getPlayer()?->sendMessage($message);
        }
    }

    /**
     * @return void
     */
    public function start(): void {
        $players = [];
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) {
                $player = $session->getPlayer();
                if (is_null($player)) continue;

                $players[] = [
                    "uuid" => $session->getUuid()->toString(),
                    "team" => strtoupper($team->getName())
                ];
            }
        }

        new PostAsyncTask(
            "/games",
            [
                "players" => $players
            ],
            function ($res) {
                if ($res instanceof RequestError) return;

                $this->id = $res["id"];
            }
        );

        foreach ($this->getSpawnPoints() as $spawnPoint) {
            $this->getWorld()->setBlock($spawnPoint->add(0, -1, 0), VanillaBlocks::AIR());
        }

        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) {
                $player = $session->getPlayer();
                if (is_null($player)) continue;

                $player->setGamemode(GameMode::SURVIVAL());
                $this->spawnPlayer($player);
            }
        }
    }

    /**
     * @return void
     */
    public function finish(): void {
        new PatchAsyncTask("/games", ["gameId" => $this->getId()], fn() => null);

        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) {
                $player = $session->getPlayer();
                if (is_null($player)) continue;
                $team->removeSession($session);

                $player->setSession(null);
                $player->getScoreboard()->remove();
                $player->setGamemode(GameMode::SPECTATOR());
                $player->clearInventories();
            }
        }

        // Display ranking
        $ranking = $this->generateRanking();
        $i = 1;
        $messages = [];
        foreach ($ranking as $uuid => $score) {
            $session = $this->getPlayerSession($uuid);
            if (is_null($session)) continue;

            $messages[] = "§7#{$i} §f{$session->getName()}: §a{$session->getKills()} kills, §c{$session->getDeaths()} deaths";
            $i++;
        }

        $this->broadcastMessage("§a§l» §r§7Game finished! Ranking:");
        $this->broadcastMessage(implode("\n", $messages));
    }

    /**
     * @return array
     */
    private function generateRanking(): array {
        $ranking = [];
        foreach ($this->teams as $team) {
            foreach ($team->getSessions() as $session) {
                $ranking[$session->getUuid()->toString()] = $session->getKills() - $session->getDeaths();
            }
        }

        arsort($ranking);

        return $ranking;
    }

    /**
     * @param GAPlayer $player
     * @return void
     */
    public function spawnPlayer(GAPlayer $player): void {
        $spawnPoint = $this->getPlayerSpawnPoint($player);
        $player->teleport($spawnPoint);
        $player->setGamemode(GameMode::SURVIVAL());
        $player->setHealth(20);
        $player->getInventory()->setItem(0, CustomiesItemFactory::getInstance()->get("hackaton:laser_gun"));
    }
}