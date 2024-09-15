<?php

namespace hackaton\task;

use hackaton\game\Game;
use hackaton\lib\customies\item\CustomiesItemFactory;
use hackaton\player\GAPlayer;
use pocketmine\player\GameMode;
use pocketmine\scheduler\CancelTaskException;

class RespawnTask extends GameTask {

    /**
     * @param Game $game
     * @param GAPlayer $player
     */
    public function __construct(Game $game, private readonly GAPlayer $player) {
        parent::__construct($game);
    }

    /**
     * @return void
     * @throws CancelTaskException
     */
    public function onRun(): void {
        parent::onRun();

        if ($this->getTime() < 7) return;

        if ($this->getTime() === 10) {
            $this->getGame()->spawnPlayer($this->player);
            $this->getHandler()->cancel();
            return;
        }

        $this->player->sendTitle(10 - $this->getTime());
    }
}