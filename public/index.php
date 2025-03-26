<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\MainController;
use App\Controllers\UserController;
use App\Models\UserModel;
use App\Storage\UserStorage;

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Обработка API-запросов
if ($requestMethod === 'GET' && strpos($requestUri, '/users') === 0) {
  $userModel = new UserModel();
  $userStorage = new UserStorage();
  $userController = new UserController($userModel, $userStorage);
  $userController->handleUserData();
  exit;
}

// Инициализация Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
$twig = new \Twig\Environment($loader, [
  'cache' => __DIR__ . '/../src/Views/twig_cache',
  'debug' => true
]);

// Основная обработка запросов
ob_start();

try {
  $request = parse_url($requestUri, PHP_URL_PATH);

  switch ($request) {
    case '/':
      (new MainController($twig))->render('layout.twig');
      break;
    case '/user/save':
      (new UserController())->save();
      break;
    default:
      (new MainController($twig))->error404();
      break;
  }
} catch (Exception $e) {
  ob_clean();
  header('HTTP/1.1 500 Internal Server Error');
  (new MainController($twig))->render('error.twig', [
    'error_code' => 500,
    'error_message' => 'Ошибка сервера: ' . $e->getMessage()
  ]);
}

ob_end_flush();
