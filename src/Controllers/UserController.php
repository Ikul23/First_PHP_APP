<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Storage\UserStorage;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

class UserController
{
  private UserModel $userModel;
  private UserStorage $userStorage;
  private TwigEnvironment $twig;

  public function __construct()
  {
    $this->userModel = new UserModel();
    $this->userStorage = new UserStorage();

    // Инициализация Twig
    $loader = new FilesystemLoader([
      __DIR__ . '/../Views',
      __DIR__ . '/../Views/partials'
    ]);
    $this->twig = new Environment($loader, [
      'debug' => true,
      'cache' => false
    ]);
  }

  public function save()
  {
    try {
      // Получаем данные из POST
      $userData = $_POST;

      // Валидация данных
      $this->userModel->validate($userData);

      // Сохранение пользователя
      $savedUser = $this->userStorage->saveUser($userData);

      // Рендер страницы с сохраненным пользователем
      echo $this->twig->render('user_save.twig', [
        'user' => $savedUser
      ]);
    } catch (\Exception $e) {
      // Обработка ошибок
      echo $this->twig->render('error.twig', [
        'error_message' => $e->getMessage()
      ]);
    }
  }

  public function showForm()
  {
    echo $this->twig->render('user_form.html');
  }
}
