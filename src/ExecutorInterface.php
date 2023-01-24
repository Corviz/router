<?php

namespace Corviz\Router;

interface ExecutorInterface
{
    /**
     * @param Route|null $route
     * @param array $params
     * @return mixed
     */
    public function execute(?Route $route, array $params): mixed;
}
