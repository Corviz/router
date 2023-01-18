# Router

Routing system based on regular expressions for PHP 8.1+ with native Middleware support

---

## How to install

```
composer install corviz/router
```
---

## Router facade (singleton)

This is the most common use scenario. That is why we provide a ready-to-use facade.

First, you have to declare you application routes:

```php
use Corviz\Router\Facade\RouterFacade as Router;
use Corviz\Router\RouteExecutor;

/*
 * Application routes
 */
Router::get('/', function() {
    return 'Hello world!';
});

Route::get('/user/(\d+)', function(int $id){
    return "Show information for user $id";
});
```

Then, execute and output the results.

```php
$params = [];
$route = Router::dispatch(params: $params);
!$route && http_response_code(404);

$output = RouteExecutor::with($route)->execute($params);
echo $output;
```
**Note:** _$output_ contains the value that was returned by controllers (a string in this example). 

### Controller classes

If you use classes as controllers, use an array as the second parameter in the route declaration.

Example controller class
```php
namespace MyApplication;

class UserController 
{
    public function show(int $id)
    {
        //Search for user information in the database
        //...
        
        return $user;
    }
}
```

Route for the controller above.
```php
Router::get('/user/(\d+)', [\MyApplication\User::class, 'show']);
```

## Supported methods

The supported methods for route declarations are: `get`,`post`,`put`,`patch`,`delete`,`options`,`head` or `any`

Each represents one HTTP method, except for `any`, which will attend to all of them

```php
use Corviz\Router\Facade\RouterFacade as Router;

Router::get('/user/(\d+)', function(int $id) { /*...*/ });
Router::post('/user/new', function() { /*...*/ });
Router::delete('/user/(\d+)', function(int $id) { /*...*/ });
```

## Middlewares

Middlewares are responsible for request pre and post processing.
We will accept callables or classes that extends `Corviz\Router\Middleware` as middlewares for your application

```php
use Corviz\Router\Middleware;

class AcceptJsonMiddleware extends Middleware
{
    public function handle(Closure $next): mixed
    {
        //Interrupts in case wrong content-type was sent
        if (!$_SERVER['CONTENT_TYPE'] != 'application/json') {
            return 'Invalid content type';
        }
        
        return $next(); //Proceed with the request
    }
}
```

To assign a Middleware do as follows:

```php
use Corviz\Router\Facade\RouterFacade as Router;

Router::any( /*...*/ )
    ->middleware(AcceptJsonMiddleware::class);
```

Or if you want to assign multiple middlewares at once:

```php
use Corviz\Router\Facade\RouterFacade as Router;

Router::any( /*...*/ )
    ->middleware([Middleware1::class, Middleware2::class]);
```

## Grouping

To group multiple routes, you must first use the `prefix` method, then just use `group` with a callable
carrying those sub-routes. For example:

```php
use Corviz\Router\Facade\RouterFacade as Router;

Router::prefix('user')->group(function() {
    Router::get('list', function() { /**/ });    
    Router::get('(\d+)', function(int $id) { /**/ });    
    Router::post('new', function() { /**/ });    
    Router::patch('(\d+)/update', function(int $id) { /**/ });    
    Router::delete('(\d+)/delete', function(int $id) { /**/ });    
});
```

This will create the following routes:

* user/list
* user/(\d+)
* user/new
* user/(\d+)/update
* user/(\d+)/delete

### Middleware for groups

You can assign middlewares for multiple routes at once by using the `middleware` method between `prefix` and `group`

```php
use Corviz\Router\Facade\RouterFacade as Router;

Router::prefix('api')
    ->middleware(CheckTokenMiddleware::class)
    ->middleware(AcceptJsonMiddleware::class)
    ->group(function() { /* ... */ });
```
