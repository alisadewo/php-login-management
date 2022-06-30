<?php 

namespace Als\Belajar\PHP\MVC\App {

	function header(string $value) {
		echo $value;
		
	} 
}

namespace Als\Belajar\PHP\MVC\Middleware {
	use PHPUnit\Framework\TestCase;

	use Als\Belajar\PHP\MVC\Config\Database;
	use Als\Belajar\PHP\MVC\Domain\User;
	use Als\Belajar\PHP\MVC\Domain\Session;
	use Als\Belajar\PHP\MVC\Repository\UserRepository;
	use Als\Belajar\PHP\MVC\Repository\SessionRepository;
	use Als\Belajar\PHP\MVC\Service\SessionService;

	class MustLoginMiddlewareTest extends TestCase
	{

		private MustLoginMiddleware $middleware;
		private UserRepository $userRepository;
		private SessionRepository $sessionRepository;

		protected function setUp(): void
		{
			$this->middleware = new MustLoginMiddleware();
			putenv("mode=test");

			$this->userRepository = new UserRepository(Database::getConnection());
			$this->sessionRepository = new SessionRepository(Database::getConnection());

			$this->sessionRepository->deleteAll();
			$this->userRepository->deleteAll();
		}

		public function testBeforeGuest()
		{
			$this->middleware->before();

			$this->expectOutputRegex("[Location: /users/login]");
		}

		public function testBeforeLoginUser()
		{
			$user = new User();
			$user->id = "ali";
			$user->name = "Ali";
			$user->password = "rahasia";
			$this->userRepository->save($user);

			$session = new Session();
			$session->id = uniqid();
			$session->userId = $user->id;
			$this->sessionRepository->save($session);

			$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

			$this->middleware->before();

			$this->expectOutputString("");
		}
	}
}

