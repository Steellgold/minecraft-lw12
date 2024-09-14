<?php

namespace hackaton\event;

use hackaton\game\Game;
use hackaton\player\GAPlayer;

class GameJoinEvent extends GameEvent {

    /***
     * @param GAPlayer $player
     * @param Game $game
     * @param int $time
     */
    public function __construct(private readonly GAPlayer $player,Game $game, int $time) {
        parent::__construct($game, $time);
    }

    /**
     * @return GAPlayer
     */
    public function getPlayer(): GAPlayer {
        return $this->player;
    }
}