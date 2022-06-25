<?php 

namespace Als\Belajar\PHP\MVC\Controller;

use Als\Belajar\PHP\MVC\App\View;

class HomeController
{

	function index()
	{
		View::render('Home/index', [
			"title" => "PHP Login Management"
		]); 
	}

}