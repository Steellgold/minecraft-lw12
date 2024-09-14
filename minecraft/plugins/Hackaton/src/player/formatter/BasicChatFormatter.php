<?php

namespace hackaton\player;

use pocketmine\lang\Translatable;
use pocketmine\player\chat\ChatFormatter;

class PlayerChatFormatter implements ChatFormatter {

    /**
     * @param string $username
     * @param string $message
     * @return Translatable|string
     */
    public function format(string $username, string $message): Translatable|string {
        return "§7{$username} §l»§r§7 {$message}";
    }
}