<?php

namespace App\Storage;

class UserStorage
{
  private string $storageFile;

  public function __construct(string $storageFile = __DIR__ . '/users.json')
  {
    $this->storageFile = $storageFile;
  }

  public function getAllUsers(): array
  {
    return $this->readUsersFromFile();
  }

  public function getUserById(string $userId): array
  {
    $users = $this->readUsersFromFile();

    foreach ($users as $user) {
      if ($user['id'] === $userId) {
        return $user;
      }
    }

    throw new \Exception('Пользователь не найден');
  }

  public function saveUser(array $userData): array
  {
    $users = $this->readUsersFromFile();

    // Если передан ID, обновляем существующую запись
    if (isset($userData['id'])) {
      $users = array_map(function ($user) use ($userData) {
        return $user['id'] === $userData['id'] ? array_merge($user, $userData) : $user;
      }, $users);
    } else {
      // Новый пользователь
      $userData['id'] = uniqid();
      $userData['created_at'] = date('Y-m-d H:i:s');
      $users[] = $userData;
    }

    $this->writeUsersToFile($users);
    return $userData;
  }

  public function deleteUser(string $userId): void
  {
    $users = $this->readUsersFromFile();

    $initialCount = count($users);
    $users = array_filter($users, function ($user) use ($userId) {
      return $user['id'] !== $userId;
    });

    if (count($users) === $initialCount) {
      throw new \Exception('Пользователь не найден');
    }

    $this->writeUsersToFile($users);
  }

  private function readUsersFromFile(): array
  {
    if (!file_exists($this->storageFile)) {
      return [];
    }

    $jsonContent = file_get_contents($this->storageFile);
    return json_decode($jsonContent, true) ?? [];
  }

  private function writeUsersToFile(array $users): void
  {
    $jsonContent = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents($this->storageFile, $jsonContent);
  }
}
