<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Als\Belajar\PHP\MVC\App\Router;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Controller\HomeController;
use Als\Belajar\PHP\MVC\Controller\UserController;

Database::getConnection('prod');

//  Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);


Router::run();