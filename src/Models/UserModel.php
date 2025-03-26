<?php

namespace App\Models;

class UserModel
{
  public function validate(array $userData): bool
  {
    if (empty($userData['name']) || strlen($userData['name']) < 2) {
      throw new \InvalidArgumentException('Name must be at least 2 characters long');
    }

    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException('Invalid email format');
    }

    if (!is_numeric($userData['age']) || $userData['age'] < 18 || $userData['age'] > 120) {
      throw new \InvalidArgumentException('Age must be a number between 18 and 120');
    }

    return true;
  }
}
