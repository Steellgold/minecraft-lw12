<?php

namespace hackaton\player\scoreboard;

use hackaton\player\GAPlayer;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\utils\TextFormat;

class Scoreboard {

    /** @var ScoreboardContent[] */
    private array $contents = [];

    /**
     * @param GAPlayer $player
     */
    public function __construct(private readonly GAPlayer $player) {
    }

    /**
     * @return void
     */
    private function setScoreboardToPlayer(): void {
        NetworkBroadcastUtils::broadcastPackets([$this->player], [
            SetDisplayObjectivePacket::create(
                "sidebar",
                $this->player->getName(),
                "§2§lHACKATON",
                "dummy",
                SetDisplayObjectivePacket::SORT_ORDER_ASCENDING
            )
        ]);
    }

    /**
     * @param string $type
     * @param ScoreboardContent $content
     * @return void
     */
    public function setContent(string $type, ScoreboardContent $content): void {
        $this->contents[$type] = $content;
    }

    /**
     * @return void
     */
    public function sendToPlayer(): void {
        $contents = [];
        foreach ($this->contents as $content) {
            $noLineText = [];
            if (!isset($contents[$content->getLine()])) {
                $contents[$content->getLine()] = [];
            }

            if ($content->getLine() === -1) {
                $noLineText[] = $content->getText();
            } else {
                $contents[$content->getColumn()][$content->getLine()] = $content->getText();
            }

            $contents[$content->getColumn()] = array_merge($contents[$content->getColumn()], $noLineText);
        }

        $textPerColumn = [];
        foreach ($contents as $column => $lines) {
            $columnText = "";
            $linesLength = count($lines);
            foreach ($lines as $line => $text) {
                if ($linesLength - 1 > $line) {
                    $columnText .= $text . "\n";
                } else {
                    $columnText .= $text;
                }
            }
            $textPerColumn[$column] = $columnText;
        }

        $this->setScoreBoardToPlayer();
        foreach ($textPerColumn as $column => $text) {
            $this->setLine($column, $text);
        }
    }

    /**
     * @param int $column
     * @param string $text
     * @return void
     */
    private function setLine(int $column, string $text): void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->player->getName();
        $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $entry->customName = $text;
        $entry->score = $column;
        $entry->scoreboardId = $column;

        NetworkBroadcastUtils::broadcastPackets([$this->player], [
            SetScorePacket::create(
                SetScorePacket::TYPE_CHANGE,
                [$entry]
            )
        ]);
    }

    /**
     * @param string $type
     * @return void
     */
    public function removeContent(string $type): void {
        unset($this->contents[$type]);
    }

    /**
     * @return void
     */
    private function remove(): void {
        NetworkBroadcastUtils::broadcastPackets([$this->player], [
            RemoveObjectivePacket::create($this->player->getName())
        ]);
    }
}