<?php 

namespace Als\Belajar\PHP\MVC\App {
	function header(string $value) {
		echo $value;
	} 
}

namespace Als\Belajar\PHP\MVC\Controller {
	use PHPUnit\Framework\TestCase;
	use Als\Belajar\PHP\MVC\Config\Database;
	use Als\Belajar\PHP\MVC\Domain\User;
	use Als\Belajar\PHP\MVC\Repository\UserRepository;

	class UserControllerTest extends TestCase
	{
		private UserController $userController;
		private UserRepository $userRepository;

		public function setUp(): void
		{
			$this->userController = new UserController();

			$connection = Database::getConnection();
			$this->userRepository = new UserRepository($connection);

			$this->userRepository->deleteAll();

			putenv("mode=test");
		}

		public function testRegister()
		{
			$this->userController->register();

			$this->expectOutputRegex("[Register]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Name]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Register new User]");
		}

		public function testPostRegisterSuccess()
		{
			$_POST['id'] = 'ali';
			$_POST['name'] = 'Ali';
			$_POST['password'] = 'rahasia';

			$this->userController->postRegister();

			$this->expectOutputRegex("[Location: /users/login]");
		}

		public function testPostRegisterValidationError()
		{
			$_POST['id'] = 'ali';
			$_POST['name'] = 'Ali';
			$_POST['password'] = '';

			$this->userController->postRegister();

			$this->expectOutputRegex("[Register]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Name]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Register new User]");
			$this->expectOutputRegex("[Id, Name, Password can not blank]");

		}

		public function testPostRegisterDuplicate()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = "rahasia";

			$this->userRepository->save($user);

			$_POST['id'] = 'ali';
			$_POST['name'] = 'Ali';
			$_POST['password'] = 'rahasia';

			$this->userController->postRegister();

			$this->expectOutputRegex("[Register]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Name]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Register new User]");
			$this->expectOutputRegex("[User is already exists]");
		}
	}
}

