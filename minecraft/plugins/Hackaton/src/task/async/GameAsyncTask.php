<?php

namespace hackaton\task\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

abstract class GameAsyncTask extends AsyncTask {

    public function __construct() {
        Server::getInstance()->getAsyncPool()->submitTask($this);
    }
}