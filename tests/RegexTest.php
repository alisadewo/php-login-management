<?php 

namespace Als\Belajar\PHP\MVC;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
	public function testRegex()
	{
		$path = "/product/12345/categories/abcde";

		$pattern = "#^/product/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

		$result = preg_match($pattern, $path, $variables);

		self::assertEquals(1, $result);

		var_dump($variables);

		array_shift($variables);
		var_dump($variables);
	}

}