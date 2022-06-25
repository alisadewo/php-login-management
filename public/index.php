<?php 

// if (isset($_SERVER['PATH_INFO'])) {
// 	echo $_SERVER['PATH_INFO'];
// } else {
// 	echo "Tidak PATH_INFO";
// }

// $path = "/index";

// if (isset($_SERVER['PATH_INFO'])){
// 	$path = $_SERVER['PATH_INFO'];
// }

// require __DIR__ . '/../app/View' . $path . '.php';

require_once __DIR__ . '/../vendor/autoload.php';

use Als\Belajar\PHP\MVC\App\Router;
use Als\Belajar\PHP\MVC\Controller\HomeController;
use Als\Belajar\PHP\MVC\Controller\ProductController;
use Als\Belajar\PHP\MVC\Middleware\AuthMiddleware;

Router::add('GET', '/product/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);
Router::add('GET', '/about', HomeController::class, 'about', [AuthMiddleware::class]);


Router::run();