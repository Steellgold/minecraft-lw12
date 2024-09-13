<?php

namespace hackaton\lib;

use hackaton\Loader;

abstract class GameLib {

    /**
     * @param Loader $loader
     * @return void
     */
    public function onEnable(Loader $loader): void { }
}