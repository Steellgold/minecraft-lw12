<?php

namespace hackaton\task\async;

use Closure;

class RequestPool {

    /** @var Closure[] */
    private static array $callbacks = [];

    /**
     * @param string $id
     * @param Closure $callback
     * @return void
     */
    public static function add(string $id, Closure $callback): void {
        self::$callbacks[$id] = $callback;
    }

    /**
     * @param string $id
     * @return Closure|null
     */
    public static function get(string $id): ?Closure {
        return self::$callbacks[$id] ?? null;
    }

    /**
     * @param string $id
     * @return void
     */
    public static function remove(string $id): void {
        unset(self::$callbacks[$id]);
    }

    /**
     * @return void
     */
    public static function clear(): void {
        self::$callbacks = [];
    }

    /**
     * @return Closure[]
     */
    public static function getCallbacks(): array {
        return self::$callbacks;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function exists(string $id): bool {
        return isset(self::$callbacks[$id]);
    }

    /**
     * @param string $id
     * @return void
     */
    public static function execute(string $id): void {
        $callback = self::get($id);
        if ($callback === null) {
            return;
        }

        $callback();
        self::remove($id);
    }
}