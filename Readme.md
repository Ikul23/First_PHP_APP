my_php_project/
│
├── docker/
│ ├── nginx/
│ │ └── default.conf
│ └── php/
│ └── Dockerfile
│
├── src/
│
│ ├── Controllers/
│ │ ├── MainController.php
│ │ └── UserController.php
│ src/Views/
├── layout.twig
├── error.twig
└── partials/
├── header.twig
├── footer.twig
└── sidebar.twig
│ ├── Models/
│ │ └── UserModel.php
│ └── Storage/
│ ├── UserStorage.php
│ ├── twig_cache/  
│ └── users.json  
├── public/
│ ├── css/
│ │ └── main.css
│ └── js/
│ └── main.js
│ ├── index.php
├── composer.json  
└── docker-compose.yml
