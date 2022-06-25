<?php 

namespace Als\Belajar\PHP\MVC\Controller;

use Als\Belajar\PHP\MVC\App\View;

class HomeController
{

	function index(): void
	{
		$model = [
			"title" => "Belajar PHP MVC",
			"content" => "Selamat Belajar PHP MVC dari Programmer Zaman Now",
		];

		// require __DIR__ . '/../View/Home/index.php';
		View::render('Home/index', $model);

	}

	function hello(): void
	{
		echo "HomeController.hello()";
	}

	function world(): void
	{
		echo "HomeController.world()";
	}

	function about(): void
	{
		echo "Author: Ali Sadewo";
	}

	function login()
	{
		$request = [
			"username" => $_POST['username'],
			"password" => $_POST['password'],
		];

		$user = [

		];


		$response = [
			"message" => "Login Sukses"
		];

		// Kirimkan response ke View
	}


}