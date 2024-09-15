<?php

namespace hackaton\task;

use hackaton\player\scoreboard\ScoreboardContent;
use pocketmine\scheduler\CancelTaskException;

class ManageGameTask extends GameTask {

    /**
     * @return void
     * @throws CancelTaskException
     */
    public function onRun(): void {
        parent::onRun();

        if ($this->getTime() % 5 !== 0) return;

        foreach ($this->getGame()->getTeams() as $team) {
            foreach ($team->getPlayers() as $player) {
                $scoreboard = $player->getScoreboard();
                $scoreboard->setContent("players", new ScoreboardContent(0, 0, "Players: (" . $this->getGame()->getPlayersCount() . "/" . $this->getGame()->getMaxPlayers() . ")"));
                $scoreboard->setContent("team", new ScoreboardContent(2, 2, "Team: " . $team->getColor() . $team->getName()));
                $scoreboard->sendToPlayer();

            }
        }
    }
}