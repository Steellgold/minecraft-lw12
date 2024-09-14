<?php

namespace hackaton\player\formatter;

use hackaton\player\GAPlayer;
use pocketmine\player\chat\ChatFormatter;

class GameChatFormatter implements ChatFormatter {

    /**
     * @param GAPlayer $player
     */
    public function __construct(private readonly GAPlayer $player) { }

    /**
     * @param string $username
     * @param string $message
     * @return string
     */
    public function format(string $username, string $message): string {
        $team = $this->player->getGame()->getTeamByPlayer($this->player);

        return $team->getColor() . "[" . strtoupper($team->getName()) . "] " . "§7{$username} §l»§r§7 {$message}";
    }
}