<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class MainController
{
  protected $twig;
  protected array $flashMessages = [];

  public function __construct()
  {
    $loader = new FilesystemLoader([
      __DIR__ . '/../Views',
      __DIR__ . '/../Views/partials'
    ]);

    $this->twig = new Environment($loader, [
      'debug' => true,
      'cache' => $this->shouldCache() ? __DIR__ . '/../Storage/twig_cache' : false,
      'auto_reload' => !$this->shouldCache()
    ]);

    $this->twig->addExtension(new DebugExtension());
    $this->setupGlobalVariables();
  }

  /**
   * Рендер шаблона с данными
   */
  public function render(string $template, array $data = []): void
  {
    $defaultData = [
      'current_time' => date('Y-m-d H:i:s'),
      'flash_messages' => $this->flashMessages,
      'is_debug' => $this->isDebugMode()
    ];

    echo $this->twig->render($template, array_merge($defaultData, $data));
  }

  /**
   * Ошибка 404
   */
  public function error404(): void
  {
    header('HTTP/1.0 404 Not Found');
    $this->render('error.twig', [
      'error_code' => 404,
      'error_message' => 'Страница не найдена'
    ]);
    exit();
  }

  /**
   * Ошибка 500
   */
  public function error500(string $message = 'Ошибка сервера'): void
  {
    header('HTTP/1.0 500 Internal Server Error');
    $this->render('error.twig', [
      'error_code' => 500,
      'error_message' => $message
    ]);
    exit();
  }

  /**
   * Добавление flash-сообщения
   */
  public function addFlashMessage(string $type, string $text): void
  {
    $this->flashMessages[] = [
      'type' => $type,
      'text' => $text
    ];
  }

  /**
   * Настройка глобальных переменных Twig
   */
  protected function setupGlobalVariables(): void
  {
    $this->twig->addGlobal('app_name', 'My PHP Application');
    $this->twig->addGlobal('current_year', date('Y'));
  }

  /**
   * Проверка режима кэширования
   */
  protected function shouldCache(): bool
  {
    return ($_ENV['APP_ENV'] ?? 'dev') === 'prod';
  }

  /**
   * Проверка режима отладки
   */
  protected function isDebugMode(): bool
  {
    return $_ENV['APP_DEBUG'] ?? false;
  }
  public function renderUserForm()
  {
    // Создаем HTML-форму динамически через Twig
    echo $this->twig->createTemplate('{{ include("user_form.html") }}')->render([]);
  }
}
