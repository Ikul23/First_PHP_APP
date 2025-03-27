First_PHP/
│
├── docker/
│ ├── nginx/
│ │ └── default.conf
│ └── php/
│ └── Dockerfile
│
├── logs/
│ ├── app/ # Логи приложения
│ │ ├── storage.log # Логи UserStorage
│ │ ├── errors.log # PHP-ошибки
│ │ └── access.log # Доступы к скриптам
│ └── nginx/
│ ├── access.log # Доступы Nginx
│ └── error.log # Ошибки Nginx
├── src/
│ ├── Controllers/
│ │ ├── MainController.php
│ │ └── UserController.php
│ │
│ ├── Models/
│ │ └── UserModel.php
│ │
│ ├── Storage/
│ │ ├── UserStorage.php
│ │ └── users.json
│ │
│ ├── Middleware/
│ │ └── AuthMiddleware.php
│ │
│ └── Routes/
│ └── routes.php
│
├── Views/
│ ├── layout.twig
│ ├── error.twig
│ ├── user_save.twig
│ ├── login.twig
│ ├── user_form.twig
│ └── partials/
│ ├── header.twig
│ ├── footer.twig
│ └── sidebar.twig
│
├── public/
│ ├── css/
│ │ └── main.css
│ ├── js/
│ │ └── main.js
│ └── index.php
│
├── composer.json
└── docker-compose.yml
