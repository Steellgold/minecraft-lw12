<?php
declare(strict_types=1);

namespace hackaton\lib\customies;

use hackaton\lib\GameLib;
use hackaton\Loader;
use hackaton\lib\customies\block\CustomiesBlockFactory;
use pocketmine\scheduler\ClosureTask;

final class Customies extends GameLib {

    /**
     * @param Loader $loader
     * @return void
     */
	public function onEnable(Loader $loader): void {
        new CustomiesListener();

		$cachePath = $loader->getDataFolder() . "idcache";
		$loader->getScheduler()->scheduleDelayedTask(new ClosureTask(static function () use ($cachePath): void {
			CustomiesBlockFactory::getInstance()->addWorkerInitHook($cachePath);
		}), 0);
	}
}
