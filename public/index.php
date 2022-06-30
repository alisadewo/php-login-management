<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Als\Belajar\PHP\MVC\App\Router;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Controller\HomeController;
use Als\Belajar\PHP\MVC\Controller\UserController;
use Als\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;
use Als\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

//  Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);


Router::run();