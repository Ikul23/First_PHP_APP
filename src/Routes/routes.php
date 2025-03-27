<?php

namespace App\Routes;

use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;

class Router
{
  private static $routes = [];
  private AuthMiddleware $authMiddleware;

  public function __construct()
  {
    $this->authMiddleware = new AuthMiddleware();
  }

  public static function get($path, $callback, $middleware = null)
  {
    self::$routes[] = [
      'method' => 'GET',
      'path' => $path,
      'callback' => $callback,
      'middleware' => $middleware
    ];
  }

  public static function post($path, $callback, $middleware = null)
  {
    self::$routes[] = [
      'method' => 'POST',
      'path' => $path,
      'callback' => $callback,
      'middleware' => $middleware
    ];
  }

  public function dispatch($uri, $method)
  {
    foreach (self::$routes as $route) {
      if ($route['path'] === $uri && $route['method'] === $method) {
        // Применяем middleware если указан
        if ($route['middleware']) {
          $middlewareMethod = $route['middleware'];
          $this->authMiddleware->$middlewareMethod();
        }

        // Вызываем callback
        $callback = $route['callback'];

        if (is_array($callback)) {
          $controller = new $callback[0]();
          $method = $callback[1];
          return $controller->$method();
        }

        return $callback();
      }
    }

    // 404 Not Found
    http_response_code(404);
    echo "Страница не найдена";
  }
}

// Определение маршрутов
$router = new Router();

$router->get('/', function () {
  echo "Главная страница";
});

$router->get('/login', [UserController::class, 'login'], 'checkGuest');
$router->post('/login', [UserController::class, 'login'], 'checkGuest');
$router->get('/logout', [UserController::class, 'logout']);

$router->get('/user/save', [UserController::class, 'showForm'], 'handle');
$router->post('/user/save', [UserController::class, 'save'], 'handle');

$router->get('/user/update/{id}', [UserController::class, 'showForm'], 'handle');
$router->post('/user/update/{id}', [UserController::class, 'update'], 'handle');
$router->get('/user/delete/{id}', [UserController::class, 'delete'], 'handle');

return $router;
