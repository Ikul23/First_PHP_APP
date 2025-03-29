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

  public static function put($path, $callback, $middleware = null)
  {
    self::$routes[] = [
      'method' => 'PUT',
      'path' => $path,
      'callback' => $callback,
      'middleware' => $middleware
    ];
  }

  public static function delete($path, $callback, $middleware = null)
  {
    self::$routes[] = [
      'method' => 'DELETE',
      'path' => $path,
      'callback' => $callback,
      'middleware' => $middleware
    ];
  }

  public function dispatch($uri, $method)
  {
    foreach (self::$routes as $route) {
      // Проверяем совпадение пути с учетом параметров
      $pattern = $this->convertToPattern($route['path']);

      if (preg_match($pattern, $uri, $matches) && $route['method'] === $method) {
        // Применяем middleware если указан
        if ($route['middleware']) {
          $middlewareMethod = $route['middleware'];
          $this->authMiddleware->$middlewareMethod();
        }

        // Извлекаем параметры из URI
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        // Вызываем callback
        $callback = $route['callback'];

        if (is_array($callback)) {
          $controller = new $callback[0]();
          $method = $callback[1];
          return $controller->$method(...array_values($params));
        }

        return $callback(...array_values($params));
      }
    }

    // 404 Not Found
    http_response_code(404);
    echo "Страница не найдена";
  }

  private function convertToPattern($path)
  {
    // Заменяем {param} на именованные группы
    $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $path);
    return '@^' . $pattern . '$@';
  }
}

// Определение маршрутов
$router = new Router();

// Основные маршруты
$router->get('/', function () {
  echo "Главная страница";
});

// Аутентификация
$router->get('/login', [UserController::class, 'login'], 'checkGuest');
$router->post('/login', [UserController::class, 'login'], 'checkGuest');
$router->get('/logout', [UserController::class, 'logout']);

// CRUD пользователей
$router->get('/user/save', [UserController::class, 'showForm'], 'handle');
$router->post('/user/save', [UserController::class, 'save'], 'handle');

$router->get('/user/update/{id}', [UserController::class, 'showForm'], 'handle');
$router->post('/user/update/{id}', [UserController::class, 'update'], 'handle');
$router->get('/user/delete/{id}', [UserController::class, 'delete'], 'handle');

// Новые маршруты для администрирования
$router->get('/admin/users', [UserController::class, 'listUsers'], 'checkAdmin');
$router->get('/admin/users/{id}/edit', [UserController::class, 'editUserForm'], 'checkAdmin');
$router->put('/admin/users/{id}', [UserController::class, 'updateUser'], 'checkAdmin');
$router->delete('/admin/users/{id}', [UserController::class, 'deleteUser'], 'checkAdmin');

// API Endpoints
$router->get('/api/users', [UserController::class, 'getAllUsersApi']);
$router->get('/api/users/{id}', [UserController::class, 'getUserApi']);
$router->post('/api/users', [UserController::class, 'createUserApi']);
$router->put('/api/users/{id}', [UserController::class, 'updateUserApi']);
$router->delete('/api/users/{id}', [UserController::class, 'deleteUserApi']);

// Профиль пользователя
$router->get('/profile', [UserController::class, 'showProfile'], 'handle');
$router->post('/profile/update', [UserController::class, 'updateProfile'], 'handle');

return $router;
