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

// Маршрутизация
$requestUri = $_SERVER['REQUEST_URI'];
$request = parse_url($requestUri, PHP_URL_PATH);

try {
  // Маршрутизация для API пользователей
  if (strpos($request, '/users') === 0) {
    $userController = new UserController();
    $userController->handleRequest();
    exit;
  }

  // Маршрутизация для Twig-рендеринга
  switch ($request) {
    case '/':
      (new MainController($twig))->render('layout.twig');
      break;
    case '/user/form':
      (new MainController($twig))->renderUserForm();
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
