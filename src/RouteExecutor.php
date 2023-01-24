<?php

namespace Corviz\Router;

use Closure;

class RouteExecutor implements ExecutorInterface
{
    protected ?Route $route;

    /**
     * @return mixed
     */
    public function execute(?Route $route, array $params): mixed
    {
        $this->route = $route;

        if (is_null($this->route)) {
            return null;
        }

        $action = $this->createCallableAction($params);
        return $this->createMiddlewarePipe($action)();
    }

    /**
     * @param array $params
     * @return Closure
     */
    protected function createCallableAction(array &$params): Closure
    {
        $action = $this->route->getAction();
        is_array($action) && $action = Closure::fromCallable([new $action[0], $action[1]]);

        return function() use (&$action, &$params) {
            return $action(...$params);
        };
    }

    /**
     * @param callable $action
     * @return Closure
     */
    protected function createMiddlewarePipe(callable $action): Closure
    {
        $middlewares = $this->route->getMiddlewares();
        $hasMiddlewares = !empty($middlewares);

        $current = $hasMiddlewares ? null : $action;
        if ($hasMiddlewares) {
            $previous = $action;
            $index = count($middlewares);

            while ($index) {
                $middleware = $middlewares[--$index];

                if (!is_callable($middleware)) {
                    $middleware = Closure::fromCallable([new $middleware, 'handle']);
                }

                $current = function() use ($middleware, $previous) {
                    return $middleware($previous);
                };

                $previous = $current;
            }
        }

        return $current;
    }
}