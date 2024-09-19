<?php

namespace hackaton\manager;

use hackaton\game\Game;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class GameManager {
    use SingletonTrait;

    /** @var Game[] */
    private array $games = [];

    /**
     * @return array
     */
    public function getGames(): array {
        return $this->games;
    }

    /**
     * @param Config $config
     * @return Promise
     */
    public function createGame(Config $config): Promise {
        $promiseResolver = new PromiseResolver();
        Game::create($config)->onCompletion(function (Game $game) use ($promiseResolver) {
            $this->games[$game->getConfigId()] = $game;
            $promiseResolver->resolve($game);
        }, fn() => null);

        return $promiseResolver->getPromise();
    }
}