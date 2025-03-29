<?php

namespace App\Storage;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class UserStorage
{
  private string $storageFile;
  private LoggerInterface $logger;

  public function __construct(
    string $storageFile = __DIR__ . '/users.json',
    LoggerInterface $logger = null
  ) {
    $this->storageFile = $storageFile;
    $this->logger = $logger ?? new NullLogger();
    $this->initStorage();
  }

  private function initStorage(): void
  {
    try {
      if (!file_exists($this->storageFile)) {
        file_put_contents($this->storageFile, '[]');
        $this->logSuccess('Storage initialized', ['file' => $this->storageFile]);
      }
    } catch (\Exception $e) {
      $this->logError('Storage init failed', $e);
      throw $e;
    }
  }

  public function getUsers(): array
  {
    $this->logger->debug('Fetching users list');
    return $this->getAllUsers();
  }

  public function getAllUsers(): array
  {
    try {
      $users = $this->readUsersFromFile();
      $this->logSuccess('Users retrieved', ['count' => count($users)]);
      return $users;
    } catch (\Exception $e) {
      $this->logError('Failed to get users', $e);
      throw $e;
    }
  }

  public function getUserById(string $userId): array
  {
    $this->logger->debug('Fetching user', ['user_id' => $userId]);

    try {
      $user = $this->findUser($userId);
      $this->logSuccess('User found', ['user_id' => $userId]);
      return $user;
    } catch (\Exception $e) {
      $this->logError('User not found', $e, ['user_id' => $userId]);
      throw $e;
    }
  }

  public function saveUser(array $userData): array
  {
    $context = [
      'user_id' => $userData['id'] ?? 'new',
      'email' => $userData['email'] ?? null
    ];

    $this->logger->info('Saving user', $context);

    try {
      $users = $this->readUsersFromFile();
      $userData = $this->prepareUserData($userData);

      $users = isset($userData['id'])
        ? $this->updateExistingUser($users, $userData)
        : $this->addNewUser($users, $userData);

      $this->writeUsersToFile($users);
      $this->logSuccess('User saved', $context);

      return $userData;
    } catch (\Exception $e) {
      $this->logError('Failed to save user', $e, $context);
      throw $e;
    }
  }

  public function deleteUser(string $userId): void
  {
    $this->logger->info('Deleting user', ['user_id' => $userId]);

    try {
      $users = $this->readUsersFromFile();
      $filteredUsers = $this->filterUser($users, $userId);

      if (count($users) === count($filteredUsers)) {
        throw new \Exception('User not found');
      }

      $this->writeUsersToFile($filteredUsers);
      $this->logSuccess('User deleted', ['user_id' => $userId]);
    } catch (\Exception $e) {
      $this->logError('Failed to delete user', $e, ['user_id' => $userId]);
      throw $e;
    }
  }

  private function findUser(string $userId): array
  {
    $users = $this->readUsersFromFile();
    foreach ($users as $user) {
      if ($user['id'] === $userId) {
        return $user;
      }
    }
    throw new \Exception('User not found');
  }

  private function prepareUserData(array $userData): array
  {
    if (!isset($userData['id'])) {
      $userData['id'] = uniqid();
      $userData['created_at'] = date('Y-m-d H:i:s');
    }
    return $userData;
  }

  private function updateExistingUser(array $users, array $userData): array
  {
    $this->logger->debug('Updating user', ['user_id' => $userData['id']]);
    return array_map(fn($user) => $user['id'] === $userData['id']
      ? array_merge($user, $userData)
      : $user, $users);
  }

  private function addNewUser(array $users, array $userData): array
  {
    $this->logger->debug('Adding new user', ['user_id' => $userData['id']]);
    $users[] = $userData;
    return $users;
  }

  private function filterUser(array $users, string $userId): array
  {
    return array_filter($users, fn($user) => $user['id'] !== $userId);
  }

  private function readUsersFromFile(): array
  {
    try {
      if (!file_exists($this->storageFile)) {
        $this->logger->notice('File not found, using empty array');
        return [];
      }

      $content = file_get_contents($this->storageFile);
      $data = json_decode($content, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException('JSON decode error: ' . json_last_error_msg());
      }

      return $data ?? [];
    } catch (\Exception $e) {
      $this->logError('Failed to read file', $e);
      throw $e;
    }
  }

  private function writeUsersToFile(array $users): void
  {
    try {
      $json = json_encode($users, JSON_PRETTY_PRINT);
      if (file_put_contents($this->storageFile, $json) === false) {
        throw new \RuntimeException('File write failed');
      }
      $this->logger->debug('Data saved', ['count' => count($users)]);
    } catch (\Exception $e) {
      $this->logError('Failed to write file', $e);
      throw $e;
    }
  }

  private function logSuccess(string $message, array $context = []): void
  {
    $this->logger->info($message, $context);
  }

  private function logError(string $message, \Exception $e, array $context = []): void
  {
    $this->logger->error($message, array_merge($context, [
      'error' => $e->getMessage(),
      'trace' => $e->getTraceAsString()
    ]));
  }
  public function isAdmin(int $userId): bool
  {
    $user = $this->getUserById($userId);
    return $user['role'] === 'admin';
  }
}
