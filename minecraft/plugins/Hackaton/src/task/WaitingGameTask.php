<?php

namespace hackaton\task;

use hackaton\event\GameWaitingEvent;
use hackaton\game\Game;
use pocketmine\scheduler\CancelTaskException;

class WaitingGameTask extends GameTask {

    /**
     * @return void
     * @throws CancelTaskException
     */
    public function onRun(): void {
        parent::onRun();
        if ($this->getGame()->getMode() === Game::MODE_WAITING) {
            (new GameWaitingEvent($this->getGame(), $this->getTime()))->call();
            // TODO: Check if the game is full and start it
            // $this->getGame()->setMode(Game::MODE_STARTING);
            // new GameStartingTask($this->getGame());
            // $this->getHandler()->cancel();
            return;
        }
    }
}