<?php

namespace hackaton\task;

use hackaton\game\Game;
use hackaton\lib\customies\item\CustomiesItemFactory;
use hackaton\Loader;
use pocketmine\player\GameMode;
use pocketmine\scheduler\CancelTaskException;

class FinishGameTask extends GameTask {

    /** @var int */
    private int $finishTime = 20;

    /**
     * @return void
     * @throws CancelTaskException
     */
    public function onRun(): void {
        parent::onRun();

        $game = $this->getGame();
        if ($game->getMode() !== Game::MODE_FINISHED) return;

        if ($this->getTime() > $this->finishTime) {
            foreach ($game->getTeams() as $team) {
                foreach ($team->getSessions() as $session) {
                    $player = $session->getPlayer();
                    if (is_null($player)) continue;

                    $player->teleport(Loader::getInstance()->getLobbySpawn());
                    $player->clearInventories();
                    $player->setGamemode(GameMode::ADVENTURE());
                    $player->getInventory()->setItem(4, CustomiesItemFactory::getInstance()->get("hackaton:game_selector"));
                }
            }
            $this->getHandler()->cancel();
        }
    }
}