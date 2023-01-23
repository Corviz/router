<?php

namespace Corviz\Router;

use Exception;

/**
 * @method Route get(string $pattern, callable|array $action)
 * @method Route post(string $pattern, callable|array $action)
 * @method Route patch(string $pattern, callable|array $action)
 * @method Route put(string $pattern, callable|array $action)
 * @method Route delete(string $pattern, callable|array $action)
 * @method Route options(string $pattern, callable|array $action)
 * @method Route head(string $pattern, callable|array $action)
 * @method Route prefix(string $pattern)
 * @method Route middleware(string|array $middleware)
 */
class Dispatcher
{
    private const SUPPORTED_HTTP_METHODS = ['get','post','put','patch','delete','options','head'];

    /**
     * @var Route[]
     */
    protected array $routes = [];

    /**
     * @param string $pattern
     * @param callable|array $action
     * @return Route
     * @throws Exception
     */
    public function any(string $pattern, callable|array $action): Route
    {
        return $this->createRoute($pattern)->action($action);
    }

    /**
     * @param string|null $method
     * @param string|null $path
     * @param array $params
     *
     * @return Route|null
     */
    public function dispatch(?string $method = null, ?string $path = null, array &$params = []): ?Route
    {
        //Prepare defaults
        is_null($method) && $method = $_SERVER['REQUEST_METHOD'] ?? null;
        is_null($path) && $path = $_SERVER['REQUEST_URI'] ?? null;

        if (!is_null($path)) {
            $path = trim($path, '/');
        }

        $found = null;

        foreach ($this->routes as $route) {
            if ($route->match($method, $path, $params)) {
                unset($params[0]);
                $params = array_values($params);
                $found = $route;
                break;
            }
        }

        return $found;
    }

    /**
     * @param string $alias
     *
     * @return Route|null
     */
    public function getRouteByAlias(string $alias): ?Route
    {
        $found = null;
        foreach ($this->routes as $route) {
            if ($route->getAlias() === $alias) {
                $found = $route;
                break;
            }
        }

        return $found;
    }

    /**
     * @param string|null $method
     * @return Route
     * @throws Exception
     */
    protected function createRoute(string $pattern, ?string $method = null): Route
    {
        $route = Route::create()->pattern($pattern);
        $method && $route->method($method);

        $this->register($route);

        return $route;
    }

    /**
     * @param Route $route
     *
     * @return void
     */
    protected function register(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return Route
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        if (in_array($name, self::SUPPORTED_HTTP_METHODS)) {
            return $this->createRoute($arguments[0], strtoupper($name))
                ->action($arguments[1]);
        }

        if ($name == 'prefix') {
            return Route::create()->pattern($arguments[0]);
        }

        if ($name == 'middleware') {
            return Route::create()->middleware($arguments[0]);
        }

        throw new Exception("Method is not supported: $name");
    }
}
