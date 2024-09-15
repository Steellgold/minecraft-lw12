<?php

namespace hackaton\task;

use hackaton\game\Game;
use hackaton\game\Team;
use hackaton\player\scoreboard\Scoreboard;
use pocketmine\scheduler\CancelTaskException;

class ManageGameTask extends GameTask {

    /**
     * @return void
     * @throws CancelTaskException
     */
    public function onRun(): void {
        parent::onRun();

        $game = $this->getGame();
        if ($game->getMode() !== Game::MODE_RUNNING) return;

        if ($this->getTime() > $this->getGame()->getDuration()) {
            $game->setMode(Game::MODE_FINISHED);
            $game->setFinish(true);
            $game->finish();
            new FinishGameTask($game);
            $this->getHandler()->cancel();
            return;
        }

        foreach ($game->getTeams() as $team) {
            $this->setScoreboard($team);
        }

        if ($game->isFinish()) {
            foreach ($game->getTeams() as $team) {
                foreach ($team->getSessions() as $session) {
                    $session->getPlayer()?->getScoreboard()->remove();
                }
            }
        }
    }

    /**
     * @param Team $team
     * @return void
     */
    private function setScoreboard(Team $team): void {
        foreach ($team->getSessions() as $session) {
            $session->getPlayer()?->getScoreboard()
                ->setLine(1, $team->getIcon())
                ->setLine(2, " " . $this->getGame()->getPlayersCount() . "/" . $this->getGame()->getMaxPlayers())
                ->setLine(3, " " . $session->getKills())
                ->setLine(4, " " . $session->getDeaths())
                ->setLine(5, " " . gmdate("i:s", $this->getGame()->getDuration() - $this->getTime()))
                ->send();
        }
    }
}