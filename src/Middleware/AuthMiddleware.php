<?php

namespace App\Middleware;

use App\Models\UserModel;

class AuthMiddleware
{
  private UserModel $userModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
  }

  public function handle()
  {
    // Начинаем сессию, если не начата
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    // Проверка авторизации через сессию
    if (isset($_SESSION['user_id'])) {
      return true;
    }

    // Проверка "Запомнить меня" через куки
    if (isset($_COOKIE['remember_token'])) {
      $user = $this->userModel->findByRememberToken($_COOKIE['remember_token']);

      if ($user) {
        // Восстанавливаем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        return true;
      }
    }

    // Перенаправление на страницу входа для неавторизованных
    header('Location: /login');
    exit();
  }

  public function checkGuest()
  {
    // Метод для страниц, доступных только неавторизованным
    if (isset($_SESSION['user_id'])) {
      header('Location: /');
      exit();
    }
    return true;
  }
}
