<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Storage\UserStorage;

class UserController
{
  private UserModel $userModel;
  private UserStorage $userStorage;

  public function __construct(UserModel $userModel, UserStorage $userStorage)
  {
    $this->userModel = $userModel;
    $this->userStorage = $userStorage;
  }

  public function handleUserData()
  {
    // Обработка GET-запроса
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $action = $_GET['action'] ?? null;

      switch ($action) {
        case 'get':
          $this->getUserData();
          break;
        case 'save':
          $this->saveUserData();
          break;
        default:
          $this->sendErrorResponse('Invalid action');
      }
    }
  }

  private function getUserData()
  {
    try {
      $users = $this->userStorage->getAllUsers();
      $this->sendJsonResponse($users);
    } catch (\Exception $e) {
      $this->sendErrorResponse('Error retrieving user data: ' . $e->getMessage());
    }
  }

  private function saveUserData()
  {
    $requiredFields = ['name', 'email', 'age'];
    $userData = [];

    foreach ($requiredFields as $field) {
      if (!isset($_GET[$field]) || empty($_GET[$field])) {
        $this->sendErrorResponse("Missing or empty field: $field");
      }
      $userData[$field] = $_GET[$field];
    }

    try {
      $this->userModel->validate($userData);
      $this->userStorage->saveUser($userData);
      $this->sendJsonResponse(['status' => 'success', 'message' => 'User data saved']);
    } catch (\Exception $e) {
      $this->sendErrorResponse('Error saving user data: ' . $e->getMessage());
    }
  }

  private function sendJsonResponse($data, $statusCode = 200)
  {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
  }

  private function sendErrorResponse($message, $statusCode = 400)
  {
    $this->sendJsonResponse(['error' => $message], $statusCode);
  }
}
