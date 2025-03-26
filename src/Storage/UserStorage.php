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
    if (!file_exists($this->storageFile)) {
      return [];
    }

    $jsonContent = file_get_contents($this->storageFile);
    return json_decode($jsonContent, true) ?? [];
  }

  public function saveUser(array $userData): void
  {
    $users = $this->getAllUsers();
    $userData['id'] = uniqid();
    $userData['created_at'] = date('Y-m-d H:i:s');

    $users[] = $userData;

    $jsonContent = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents($this->storageFile, $jsonContent);
  }
}
