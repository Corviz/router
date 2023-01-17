<?php

namespace Corviz\Router;

use Closure;

class Route
{
    /**
     * @var Route
     */
    private static ?Route $groupTo = null;

    private mixed $action = null;
    private ?string $alias = null;
    private ?string $method = null;
    private array $middlewares = [];
    private string $pattern = '';

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @param callable|array $action
     * @return $this
     */
    public function action(callable|array $action): static
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function alias(string $alias): static
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @param callable $fn
     * @return $this
     */
    public function group(callable $fn): static
    {
        $previousBase = self::$groupTo;
        self::$groupTo = $this;
        $fn();
        self::$groupTo = $previousBase;

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function method(string $method): static
    {
        $this->method = $method;
        return $this;
    }

    public function middleware(string|array $middleware)
    {
        $middleware = (array) $middleware;

        //Filter contents
        $middleware = array_values(array_filter($middleware, function($item){
            return is_callable($item) || is_subclass_of($item, Middleware::class);
        }));

        array_push($this->middlewares, ...$middleware);

        return $this;
    }

    /**
     * @param string $pattern
     * @return $this
     */
    public function pattern(string $pattern)
    {
        $this->pattern .= trim($pattern, '/');
        return $this;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $matches
     * @return int|false
     */
    public function match(?string $method, ?string $path, array &$matches): int|false
    {
        if ($this->method && $method !== $this->method) {
            return false;
        }

        return preg_match("#^$this->pattern$#", $path, $matches);
    }

    /**
     * @return mixed
     */
    public function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Private constructor prevent new instances
     */
    private function __construct()
    {
        if (!is_null(self::$groupTo)) {
            $base = self::$groupTo;
            array_push($this->middlewares, ...$base->middlewares);
            $this->pattern = trim($base->pattern, '/').'/?';
        }
    }
}
