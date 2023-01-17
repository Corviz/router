<?php

use Corviz\Router\Facade\RouterFacade as Router;
use Corviz\Router\RouteExecutor;

ini_set('display_errors', 1);
require __DIR__.'/vendor/autoload.php';

$router = new Router();

Router::get('/usuarios/(?\'id\'\d+)/(?\'nome\'\w+)', function($id, $nome) {
    echo "Usuario: $id ($nome)";
});

Router::get('/', function() {
    echo "Hello world";
});

$matches = [];
$route = Router::dispatch(params: $matches);
!$route && http_response_code(404);

print_r($matches);

//RouteExecutor::with($route)->execute($matches);