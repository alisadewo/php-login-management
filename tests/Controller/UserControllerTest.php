<?php 

namespace Als\Belajar\PHP\MVC\App {

	function header(string $value) {
		echo $value;
		
	} 
}

namespace Als\Belajar\PHP\MVC\Service {
	function setcookie(string $name, string $value) {
		echo "$name: $value";
	}
}

namespace Als\Belajar\PHP\MVC\Controller {
	use PHPUnit\Framework\TestCase;
	use Als\Belajar\PHP\MVC\Config\Database;
	use Als\Belajar\PHP\MVC\Domain\User;
	use Als\Belajar\PHP\MVC\Domain\Session;
	use Als\Belajar\PHP\MVC\Repository\UserRepository;
	use Als\Belajar\PHP\MVC\Repository\SessionRepository;
	use Als\Belajar\PHP\MVC\Service\SessionService;

	class UserControllerTest extends TestCase
	{
		private UserController $userController;
		private UserRepository $userRepository;
		private SessionRepository $sessionRepository;


		public function setUp(): void
		{
			$this->userController = new UserController();

			$connection = Database::getConnection();

			$this->sessionRepository = new SessionRepository($connection);
			$this->userRepository = new UserRepository($connection);

			$this->sessionRepository->deleteAll();
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
			$this->expectOutputRegex("[User Id already exists]");
		}

		public function testLoginForm()
		{
			$this->userController->login();

			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Login user]");
		}

		public function testLoginSuccess()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);

			$this->userRepository->save($user);

			$_POST['id'] = 'ali';
			$_POST['password'] = 'rahasia';

			$this->userController->postLogin();

			$this->expectOutputRegex("[Location: /]");
			$this->expectOutputRegex("[X-SESSION: ]");
		}

		public function testloginValidationError()
		{
			$_POST['id'] = '';
			$_POST['password'] = '';
			$this->userController->postLogin();

			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Login user]");
			$this->expectOutputRegex("[Id, Password can not blank]");
		}

		public function testLoginUserNotFound()
		{
			$_POST['id'] = 'not found';
			$_POST['password'] = 'not found';
			$this->userController->postLogin();

			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Login user]");
			$this->expectOutputRegex("[id or password is wrong]");
		}

		public function testLoginWrongPassword()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);

			$this->userRepository->save($user);

			$_POST['id'] = 'ali';
			$_POST['password'] = 'salah';
			$this->userController->postLogin();

			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Login user]");
			$this->expectOutputRegex("[id or password is wrong]");
		}

		public function testLogout()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$this->userController->logout();

			$this->expectOutputRegex("[Location: /]");
			$this->expectOutputRegex("[X-SESSION: ]");
		}

		public function testUpdateProfile()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$this->userController->updateProfile();
			
			$this->expectOutputRegex('[Profile]');
			$this->expectOutputRegex('[Id]');
			$this->expectOutputRegex('[ali]');
			$this->expectOutputRegex('[Name]');
			$this->expectOutputRegex('[Ali]');

			
		}

		public function testPostUpdateProfile()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$_POST['name'] = 'Budi';
			$this->userController->postUpdateProfile();

			$this->expectOutputRegex('[Location: /]');

			$result = $this->userRepository->findById($user->id);
			self::assertEquals("Budi", $result->name);
		}

		public function testPostUpdateProfileValidationError()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$_POST['name'] = "";
			$this->userController->postUpdateProfile();

			$this->expectOutputRegex("[Profile]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[ali]");
			$this->expectOutputRegex("[Name]");
			$this->expectOutputRegex("[Id, Name can not blank]");
		}

		public function testUpdatePassword()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$this->userController->updatePassword();

			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[ali]");
		}

		public function testPostUpdatePasswordSuccess()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$_POST['oldPassword'] = "rahasia";
			$_POST['newPassword'] = "new";

			$this->userController->postUpdatePassword();

			$this->expectOutputRegex("[Location: /]");

			$result = $this->userRepository->findById($user->id);

			self::assertTrue(password_verify("new", $result->password));


		}

		public function testPostUpdatePasswordValidateError()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$_POST['oldPassword'] = "";
			$_POST['newPassword'] = "";

			$this->userController->postUpdatePassword();

			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[ali]");
			$this->expectOutputRegex("[Id, Old Password, New Password can not blank]");

		}

		public function testPostUpdatePasswordWrongOldPassword()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = password_hash("rahasia", PASSWORD_BCRYPT);
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$_POST['oldPassword'] = "salah";
			$_POST['newPassword'] = "new";

			$this->userController->postUpdatePassword();

			$this->expectOutputRegex("[Password]");
			$this->expectOutputRegex("[Id]");
			$this->expectOutputRegex("[ali]");
			$this->expectOutputRegex("[Old password is wrong]");
		}


	}
}

