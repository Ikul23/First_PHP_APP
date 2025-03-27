<?php

namespace App\Models;

use App\Storage\UserStorage;

class UserModel
{
  private UserStorage $userStorage;

  public function __construct()
  {
    $this->userStorage = new UserStorage();
  }

  public function validate(array $userData): bool
  {
    // Существующая валидация
    if (
      empty($userData['name']) ||
      strlen($userData['name']) < 2 ||
      strlen($userData['name']) > 50
    ) {
      throw new \InvalidArgumentException('Имя должно быть от 2 до 50 символов');
    }

    if (
      empty($userData['email']) ||
      !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)
    ) {
      throw new \InvalidArgumentException('Некорректный формат email');
    }

    if (
      !isset($userData['age']) ||
      !is_numeric($userData['age']) ||
      $userData['age'] < 18 ||
      $userData['age'] > 120
    ) {
      throw new \InvalidArgumentException('Возраст должен быть от 18 до 120 лет');
    }

    return true;
  }

  public function authenticateUser(string $email, string $password, bool $remember = false)
  {
    $users = $this->userStorage->getUsers();

    foreach ($users as &$user) {
      if ($user['email'] === $email) {
        if (password_verify($password, $user['password'])) {
          if ($remember) {
            $token = bin2hex(random_bytes(32));
            $tokenExpiry = time() + (30 * 24 * 60 * 60); // 30 дней

            $user['remember_token'] = $token;
            $user['token_expiry'] = $tokenExpiry;

            // Обновляем пользователей
            $this->userStorage->saveUsers($users);

            // Устанавливаем куки
            setcookie('remember_token', $token, $tokenExpiry, '/', '', true, true);
          }
          return $user;
        }
        throw new \InvalidArgumentException('Неверный пароль');
      }
    }

    throw new \InvalidArgumentException('Пользователь не найден');
  }

  public function logout()
  {
    if (isset($_COOKIE['remember_token'])) {
      $token = $_COOKIE['remember_token'];
      $users = $this->userStorage->getUsers();

      foreach ($users as &$user) {
        if (isset($user['remember_token']) && $user['remember_token'] === $token) {
          unset($user['remember_token']);
          unset($user['token_expiry']);
          break;
        }
      }

      $this->userStorage->saveUsers($users);
      setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }

    session_unset();
    session_destroy();
  }

  public function findByRememberToken($token)
  {
    $users = $this->userStorage->getUsers();
    foreach ($users as $user) {
      if (
        isset($user['remember_token']) &&
        $user['remember_token'] === $token &&
        isset($user['token_expiry']) &&
        $user['token_expiry'] > time()
      ) {
        return $user;
      }
    }
    return null;
  }
}
