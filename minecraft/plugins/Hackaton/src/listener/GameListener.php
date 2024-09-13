<?php

namespace hackaton\listener;

use hackaton\Loader;
use pocketmine\event\Listener;
use pocketmine\Server;

abstract class GameListener implements Listener {

    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents($this, Loader::getInstance());
    }
}