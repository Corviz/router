<?php

namespace Corviz\Router\Facade;

use Corviz\Router\Dispatcher;
use Corviz\Router\Route;

/**
 * @method static Route any(string $pattern, callable|array $action)
 * @method static Route get(string $pattern, callable|array $action)
 * @method static Route post(string $pattern, callable|array $action)
 * @method static Route patch(string $pattern, callable|array $action)
 * @method static Route put(string $pattern, callable|array $action)
 * @method static Route delete(string $pattern, callable|array $action)
 * @method static Route options(string $pattern, callable|array $action)
 * @method static Route head(string $pattern, callable|array $action)
 * @method static Route prefix(string $pattern)
 * @method static Route middleware(string|array $middleware)
 */
class RouterFacade
{
    private static ?Dispatcher $dispatcher = null;

    public static function dispatch(?string $method = null, ?string $path = null, array &$params = []): ?Route
    {
        return self::dispatcher()->dispatch($method, $path, $params);
    }

    /**
     * @return Dispatcher
     */
    protected static function dispatcher(): Dispatcher
    {
        if (!self::$dispatcher) {
            self::$dispatcher = new Dispatcher();
        }

        return self::$dispatcher;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return self::dispatcher()->$name(...$arguments);
    }

    public function __construct()
    {
        //Prevent new instances
    }
}