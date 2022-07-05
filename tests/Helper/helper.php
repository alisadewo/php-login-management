<?php 

namespace Als\Belajar\PHP\MVC\App {

	function header(string $value) {
		echo $value;
		
	} 
}

namespace Als\Belajar\PHP\MVC\Service {

	function setcookie(string $name, string $value)
	{
		echo "$name: $value";
	}
}