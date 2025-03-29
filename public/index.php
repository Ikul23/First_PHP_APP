<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\MainController;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Настройка CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

// Инициализация Twig
$loader = new FilesystemLoader([
  __DIR__ . '/../src/Views',
  __DIR__ . '/../src/Views/partials'
]);
$twig = new Environment($loader, [
  'cache' => __DIR__ . '/../src/Views/twig_cache',
  'debug' => true
]);

// Получаем путь запроса
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

try {
  // Основная маршрутизация
  switch ($requestUri) {
    case '/':
      (new MainController($twig))->render('layout.twig');
      break;

    case '/users':
      if ($method === 'GET') {
        (new UserController($twig))->listUsers();
      } else {
        (new MainController($twig))->error404();
      }
      break;

    case '/user/form':
      (new MainController($twig))->renderUserForm();
      break;

    // API Endpoints
    case preg_match('/\/users\/(\d+)\/delete/', $requestUri) ? true : false:
      if ($method === 'DELETE') {
        $userId = (int) preg_replace('/\/users\/(\d+)\/delete/', '$1', $requestUri);
        (new UserController())->deleteUser($userId);
      } else {
        (new MainController($twig))->error404();
      }
      break;

    default:
      (new MainController($twig))->error404();
      break;
  }
} catch (Exception $e) {
  header('HTTP/1.1 500 Internal Server Error');
  (new MainController($twig))->render('error.twig', [
    'error_code' => 500,
    'error_message' => 'Ошибка сервера: ' . $e->getMessage()
  ]);
}
