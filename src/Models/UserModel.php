<?php

namespace App\Models;

class UserModel
{
  public function validate(array $userData): bool
  {
    // Проверка имени
    if (
      empty($userData['name']) ||
      strlen($userData['name']) < 2 ||
      strlen($userData['name']) > 50
    ) {
      throw new \InvalidArgumentException('Имя должно быть от 2 до 50 символов');
    }

    // Проверка email
    if (
      empty($userData['email']) ||
      !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)
    ) {
      throw new \InvalidArgumentException('Некорректный формат email');
    }

    // Проверка возраста
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
}
