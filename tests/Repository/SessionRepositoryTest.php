<?php 

namespace Als\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Domain\Session;


class SessionRepositoryTest extends TestCase
{
	private SessionRepository $sessionRepository;

	protected function setUp(): void
	{
		$connection = Database::getConnection();
		$this->sessionRepository = new SessionRepository($connection);
		$this->sessionRepository->deleteAll();
	}

	public function testSaveSuccess()
	{
		$session = new Session();
		$session->id = uniqid();
		$session->userId = "ali";

		$this->sessionRepository->save($session);

		$result = $this->sessionRepository->findById($session->id);
		self::assertEquals($session->id, $result->id);
		self::assertEquals($session->userId, $result->userId);
	
	}

	public function testDeleteByIdSuccess()
	{
		$session = new Session();
		$session->id = uniqid();
		$session->userId = "ali";

		$this->sessionRepository->save($session);

		$result = $this->sessionRepository->findById($session->id);
		self::assertEquals($session->id, $result->id);
		self::assertEquals($session->userId, $result->userId);

		$this->sessionRepository->deleteById($session->id);

		$result = $this->sessionRepository->findById($session->id);
		self::assertNull($result);

	}

	public function testFindByIdNotFound()
	{
		$result = $this->sessionRepository->findById("Not Found");
		self::assertNull($result);
	}
}