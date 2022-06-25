<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Als\Belajar\PHP\MVC\App\Router;
use Als\Belajar\PHP\MVC\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);


Router::run();