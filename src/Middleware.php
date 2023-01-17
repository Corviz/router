<?php

namespace Corviz\Router;

use Closure;

abstract class Middleware
{
    /**
     * @param Closure $next
     *
     * @return mixed
     */
    public abstract function handle(Closure $next): mixed;
}
