<?php

namespace hackaton\event;

use hackaton\game\Game;

class GameStartingEvent extends GameEvent {

    /***
     * @param Game $game
     * @param int $time
     */
    public function __construct(Game $game, int $time) {
        parent::__construct($game, $time);
    }
}