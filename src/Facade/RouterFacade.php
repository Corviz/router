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
 * @method static mixed dispatch(?string $method = null, ?string $path = null)
 * @method static Route|null found()
 * @method static Route|null getRouteByAlias(string $alias)
 */
class RouterFacade
{
    protected static ?Dispatcher $dispatcher = null;

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
}