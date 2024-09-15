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

        if ($this->getGame()->isFinish()) {
            $this->getHandler()->cancel();
            return;
        }

        if ($this->getTime() === 10) {
            $this->getGame()->spawnPlayer($this->player);
            $this->getHandler()->cancel();
        }

        $this->player->sendActionBarMessage("§cRespawn in §l§4» §r" . str_repeat("§c▌", 10 - $this->getTime()) . str_repeat("§7▌", $this->getTime()));
    }
}