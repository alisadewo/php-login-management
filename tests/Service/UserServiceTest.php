<?php 

namespace Als\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Domain\User;
use Als\Belajar\PHP\MVC\Exception\ValidationException;
use Als\Belajar\PHP\MVC\Repository\UserRepository; 
use Als\Belajar\PHP\MVC\Model\UserRegisterRequest; 


class UserServiceTest extends TestCase
{

	private UserService $userService;
	private UserRepository $userRepository;

	protected function setUp(): void
	{
		$connection = Database::getConnection();
		$this->userRepository = new UserRepository($connection);
		$this->userService = new UserService($this->userRepository);

		$this->userRepository->deleteAll();
	}

	public function testRegisterSuccess()
	{
		$request = new UserRegisterRequest();
		$request->id = "ali";
		$request->name = "Ali";
		$request->password = "rahasia";

		$response = $this->userService->register($request);

		self::assertEquals($request->id, $response->user->id);
		self::assertEquals($request->name, $response->user->name);
		self::assertNotEquals($request->password, $response->user->password);

		self::assertTrue(password_verify($request->password, $response->user->password));


	}

	public function testRegisterFailed()
	{
		$this->expectException(ValidationException::class);

		$request = new UserRegisterRequest();
		$request->id = "";
		$request->name = "";
		$request->password = "";

		$this->userService->register($request);
	}

	public function testRegisterDuplicate()
	{
		$user = new User();
		$user->id = "ali";
		$user->name = "Ali";
		$user->password = "rahasia";

		$this->userRepository->save($user);

		$this->expectException(ValidationException::class);

		$request = new UserRegisterRequest();
		$request->id = "ali";
		$request->name = "Ali";
		$request->password = "rahasia";

		$this->userService->register($request);
	}
}