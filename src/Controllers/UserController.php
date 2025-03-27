<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Storage\UserStorage;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class UserController
{
  private UserModel $userModel;
  private UserStorage $userStorage;
  private Environment $twig;

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

  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';
      $remember = isset($_POST['remember']) ? true : false;

      try {
        $user = $this->userModel->authenticateUser($email, $password, $remember);

        // Устанавливаем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header('Location: /');
        exit();
      } catch (\Exception $e) {
        echo $this->twig->render('login.twig', [
          'error' => $e->getMessage()
        ]);
      }
    } else {
      echo $this->twig->render('login.twig');
    }
  }

  public function logout()
  {
    $this->userModel->logout();
    header('Location: /login');
    exit();
  }

  public function save()
  {
    try {
      $userData = $_POST;
      $this->userModel->validate($userData);
      $savedUser = $this->userStorage->saveUser($userData);

      echo $this->twig->render('user_save.twig', [
        'user' => $savedUser
      ]);
    } catch (\Exception $e) {
      echo $this->twig->render('error.twig', [
        'error_message' => $e->getMessage()
      ]);
    }
  }

  public function showForm($id = null)
  {
    $user = $id ? $this->userStorage->getUserById($id) : null;
    echo $this->twig->render('user_form.html', [
      'user' => $user
    ]);
  }

  public function update($id)
  {
    try {
      $userData = $_POST;
      $this->userModel->validate($userData);
      $updatedUser = $this->userStorage->updateUser($id, $userData);

      echo $this->twig->render('user_save.twig', [
        'user' => $updatedUser
      ]);
    } catch (\Exception $e) {
      echo $this->twig->render('error.twig', [
        'error_message' => $e->getMessage()
      ]);
    }
  }

  public function delete($id)
  {
    try {
      $this->userStorage->deleteUser($id);
      header('Location: /users');
      exit();
    } catch (\Exception $e) {
      echo $this->twig->render('error.twig', [
        'error_message' => $e->getMessage()
      ]);
    }
  }
}
