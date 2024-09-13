<?php

namespace hackaton\form\lobby;

use hackaton\GAPlayer;
use hackaton\lib\form\SimpleForm;

class ChoseGameForm extends SimpleForm {

    /**
     * @param GAPlayer $player
     * @return void
     */
    protected function create(GAPlayer $player): void {
        $this->setTitle("Chose a game");
        $this->setContent("Chose a game to play");
        $this->addButton("Laser Game");
        $this->addButton("Bed Wars");
    }

    /**
     * @param GAPlayer $player
     * @param $data
     * @return void
     */
    protected function handle(GAPlayer $player, $data): void {
        // TODO: Implement handle() method.
    }
}