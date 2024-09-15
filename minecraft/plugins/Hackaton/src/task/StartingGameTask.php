<?php

namespace hackaton\task;

use hackaton\event\GameStartingEvent;
use hackaton\game\Game;
use pocketmine\world\sound\NoteInstrument;
use pocketmine\world\sound\NoteSound;

class StartingGameTask extends GameTask {

    public function onRun(): void {
        parent::onRun();

        $game = $this->getGame();
        if ($game->getMode() !== Game::MODE_STARTING) return;

        (new GameStartingEvent($game, $this->getTime()))->call();

        // If the min number of players is not reached, return to waiting mode
        if ($game->getPlayersCount() < $game->getMinPlayers()) {
            $game->setMode(Game::MODE_WAITING);
            new WaitingGameTask($game);
            $this->getHandler()->cancel();
            return;
        }

        if ($this->getTime() === 10) {
            $game->start();
            //$game->broadcastTip("§eGame start §l§g» §r" . str_repeat("§e█", 10));
            $game->broadcastSound(new NoteSound(NoteInstrument::PIANO(), 25));
            $game->setMode(Game::MODE_RUNNING);
            new ManageGameTask($game);
            $this->getHandler()->cancel();
        }

        // Broadcast the countdown message
        $game->broadcastActionBarMessage("§eThe game starts in  §l§g» §r" . str_repeat("§e▌", 10 - $this->getTime()) . str_repeat("§7▌", $this->getTime()));
        $game->broadcastSound(new NoteSound(NoteInstrument::PIANO(), 12));
    }
}