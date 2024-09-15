<?php

namespace hackaton\player\scoreboard;

use hackaton\player\GAPlayer;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard {

    /** @var string */
    public const FLAG_CORNER = "§m§b";

    /** @var string */
    private string $objectiveName;

    /** @var string */
    private string $displayName = self::FLAG_CORNER;

    /** @var array<int, string> */
    private array $lines = [];

    /**
     * @param GAPlayer $player
     */
    public function __construct(private readonly GAPlayer $player) {
        $this->objectiveName = $this->player->getUniqueId()->toString();
    }

    /**
     * @param GAPlayer $player
     * @return Scoreboard
     */
    public static function create(GAPlayer $player): Scoreboard {
        $scoreboard = new Scoreboard($player);
        $player->setScoreboard($scoreboard);
        return $scoreboard;
    }

    /**
     * @return GAPlayer
     */
    public function getPlayer(): GAPlayer {
        return $this->player;
    }

    /**
     * @return string
     */
    public function getObjectiveName(): string {
        return $this->objectiveName;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string {
        return $this->displayName;
    }

    /**
     * @return array
     */
    public function getLines(): array {
        return $this->lines;
    }

    /**
     * @param int $row
     * @param string $text
     * @return Scoreboard
     */
    public function setLine(int $row, string $text): Scoreboard {
        $this->lines[$row] = $text;
        return $this;
    }

    /**
     * @return void
     */
    public function send(): void {
        NetworkBroadcastUtils::broadcastPackets([$this->getPlayer()], [
            SetDisplayObjectivePacket::create(
                SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR,
                $this->objectiveName,
                $this->displayName,
                "dummy",
                SetDisplayObjectivePacket::SORT_ORDER_ASCENDING
            ),
            SetScorePacket::create(
                SetScorePacket::TYPE_REMOVE,
                array_map(function (int $row): ScorePacketEntry {
                    $entry = new ScorePacketEntry();
                    $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
                    $entry->objectiveName = $this->objectiveName;
                    $entry->scoreboardId = $row;
                    $entry->score = $row;
                    return $entry;
                }, array_keys($this->lines)),
            ),
            SetScorePacket::create(
                SetScorePacket::TYPE_CHANGE,
                array_map(function (int $row, string $text): ScorePacketEntry {
                    $entry = new ScorePacketEntry();
                    $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
                    $entry->objectiveName = $this->objectiveName;
                    $entry->scoreboardId = $row;
                    $entry->score = $row;
                    $entry->customName = $text;
                    return $entry;
                }, array_keys($this->lines), array_values($this->lines)),
            )
        ]);
    }

    public function remove(): void {
        NetworkBroadcastUtils::broadcastPackets([$this->player], [
            RemoveObjectivePacket::create($this->objectiveName)
        ]);
    }
}