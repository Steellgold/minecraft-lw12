<?php

namespace hackaton\form;

use hackaton\GAPlayer;
use hackaton\lib\SimpleForm;

class ChoseGameForm extends SimpleForm {

    /**
     * @param GAPlayer $player
     * @return void
     */
    protected function create(GAPlayer $player): void {
        $this->setTitle("Chose a game");
        $this->setContent("Chose a game to play");
        $this->addButton("Game 1");
        $this->addButton("Game 2");
        $this->addButton("Game 3");
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