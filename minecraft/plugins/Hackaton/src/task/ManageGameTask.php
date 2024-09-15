<?php

namespace hackaton\task;

use hackaton\game\Game;
use hackaton\game\Team;
use hackaton\player\scoreboard\ScoreboardContent;
use pocketmine\scheduler\CancelTaskException;

class ManageGameTask extends GameTask {

    /**
     * @return void
     * @throws CancelTaskException
     */
    public function onRun(): void {
        parent::onRun();

        if ($this->getTime() >= $this->getGame()->getDuration()) {
            $this->getGame()->setMode(Game::MODE_FINISHED);
            $this->getGame()->setFinish(true);
            $this->getGame()->finish();
            $this->getHandler()->cancel();
            return;
        }

        foreach ($this->getGame()->getTeams() as $team) {
            $this->setScoreboard($team);
        }

        if ($this->getGame()->isFinish()) {
            foreach ($this->getGame()->getTeams() as $team) {
                foreach ($team->getPlayers() as $player) {
                    $player->getScoreboard()->remove();
                }
            }
        }
    }

    /**
     * @param Team $team
     * @return void
     */
    private function setScoreboard(Team $team): void {
        foreach ($team->getPlayers() as $player) {
            $player->getScoreboard()
                ->setLine(1, $team->getIcon())
                ->setLine(2, " " . $this->getGame()->getPlayersCount() . "/" . $this->getGame()->getMaxPlayers())
                ->setLine(3, " 0")
                ->setLine(4, " 0")
                ->setLine(5, " " . gmdate("i:s", $this->getGame()->getDuration() - $this->getTime()))
                ->send();
        }
    }
}