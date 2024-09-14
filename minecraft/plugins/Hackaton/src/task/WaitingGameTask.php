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
        if ($this->getGame()->getMode() !== Game::MODE_WAITING) return;

        (new GameWaitingEvent($this->getGame(), $this->getTime()))->call();

        // Start the game if is full
        if ($this->getGame()->getPlayersCount() === $this->getGame()->getMaxPlayers()) {
            $this->getGame()->setMode(Game::MODE_STARTING);
            new StartingGameTask($this->getGame());
            $this->getHandler()->cancel();
            return;
        }

        // Start the game if the time is greater than 30 seconds and the minimum number of players is reached
        if ($this->getGame()->getPlayersCount() >= $this->getGame()->getMinPlayers() && $this->getTime() >= 30) {
            $this->getGame()->setMode(Game::MODE_STARTING);
            new StartingGameTask($this->getGame());
            $this->getHandler()->cancel();
            return;
        }
    }
}