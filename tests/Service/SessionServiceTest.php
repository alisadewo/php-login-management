<?php 

namespace Als\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Domain\User;
use Als\Belajar\PHP\MVC\Domain\Session;
use Als\Belajar\PHP\MVC\Repository\SessionRepository;
use Als\Belajar\PHP\MVC\Repository\UserRepository;

function setcookie(string $name, string $value)
{
	echo "$name: $value";
}

class SessionServiceTest extends TestCase
{
	private SessionService $sessionService;
	private SessionRepository $sessionRepository;
	private UserRepository $userRepository;

	protected function setUp():void
	{
		$connection = Database::getConnection();
		$this->sessionRepository = new SessionRepository($connection);
		$this->userRepository = new UserRepository($connection);
		$this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

		$this->sessionRepository->deleteAll();
		$this->userRepository->deleteAll();

		$user = new User();
		$user->id = "ali";
		$user->name = "Ali";
		$user->password = "rahasia";

		$this->userRepository->save($user);
	}

	public function testCreate()
	{
		$session = $this->sessionService->create("ali");

		$this->expectOutputRegex("[X-SESSION: $session->id]");

		$result = $this->sessionRepository->findById($session->id);

		self::assertEquals("ali", $result->userId);
	}

	public function testDestroy()
	{
		$session = new Session();
		$session->id = uniqid();
		$session->userId = "ali";

		$this->sessionRepository->save($session);

		$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

		$this->sessionService->destroy();

		$this->expectOutputRegex('[X-SESSION: ]');

		$result = $this->sessionRepository->findById($session->id);
		self::assertNull($result);
	}

	public function testCurrentSuccess()
	{
		$session = new Session();
		$session->id = uniqid();
		$session->userId = "ali";

		$this->sessionRepository->save($session);

		$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

		$user = $this->sessionService->current();

		self::assertEquals($session->userId, $user->id);
	}

	public function testCurrentNotFound()
	{
		$session = new Session();
		$session->id = uniqid();
		$session->userId = "ali";

		$this->sessionRepository->save($session);

		$_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

		$user = $this->sessionService->current();

		self::assertEquals($session->userId, $user->id);
	}
}