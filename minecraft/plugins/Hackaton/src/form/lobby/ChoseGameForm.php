<?php

namespace hackaton\form\lobby;

use hackaton\GAPlayer;
use hackaton\lib\form\SimpleForm;
use hackaton\Loader;

class ChoseGameForm extends SimpleForm {

    /**
     * @param GAPlayer $player
     * @return void
     */
    protected function create(GAPlayer $player): void {
        $this->setTitle("Chose a game");
        $this->setContent("Chose a game to play");
        $this->addButton("Laser Game", -1, "", "laser-game");
        // $this->addButton("Bed Wars");
    }

    /**
     * @param GAPlayer $player
     * @param $data
     * @return void
     */
    protected function handle(GAPlayer $player, $data): void {
        $player->sendMessage(Loader::PREFIX . "Creating game...");
        $config = match ($data) {
            "laser-game" => Loader::getInstance()->getLaserGameConfig(),
            // 1 => "bed-wars.yml",
            default => null
        };

        if (is_null($config)) return;

        $player->joinGame($config);
    }
}