<?php 

namespace Als\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Domain\User;
use Als\Belajar\PHP\MVC\Exception\ValidationException;
use Als\Belajar\PHP\MVC\Repository\UserRepository; 
use Als\Belajar\PHP\MVC\Repository\SessionRepository; 
use Als\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Als\Belajar\PHP\MVC\Model\UserRegisterRequest; 
use Als\Belajar\PHP\MVC\Model\UserLoginRequest; 


class UserServiceTest extends TestCase
{

	private UserService $userService;
	private UserRepository $userRepository;
	private SessionRepository $sessionRepository;

	protected function setUp(): void
	{
		$connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();
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

	public function testLoginNotFound()
	{
		$this->expectException(ValidationException::class);

		$request = new UserloginRequest();
		$request->id = "ali";
		$request->password = "ali";

		$this->userService->login($request);
	}

	public function testLoginWrongPassword()
	{
		$user = new User();
		$user->id = "ali";
		$user->name = "Ali";
		$user->password = password_hash("rahasia", PASSWORD_BCRYPT);

		$this->expectException(ValidationException::class);

		$request = new UserloginRequest();
		$request->id = "ali";
		$request->password = "salah";

		$this->userService->login($request);
	}

	public function testLoginSuccess()
	{
		$user = new User();
		$user->id = "ali";
		$user->name = "Ali";
		$user->password = password_hash("rahasia", PASSWORD_BCRYPT);

		$this->expectException(ValidationException::class);

		$request = new UserloginRequest();
		$request->id = "ali";
		$request->password = "rahasia";

		$response = $this->userService->login($request);

		self::assertEquals($request->id, $response->id);
		self::assertTrue(password_verify($request->password, $response->password));
	}

	public function testUpdateSuccess()
	{
		$user = new User();
		$user->id = "ali";
		$user->name = "Ali";
		$user->password = password_hash("rahasia", PASSWORD_BCRYPT);

		$this->userRepository->save($user);

		$request = new UserProfileUpdateRequest();
		$request->id = "ali";
		$request->name = "Budi";

		$this->userService->updateProfile($request);

		$result = $this->userRepository->findById($user->id);

		self::assertEquals($request->name, $result->name);


	}

	public function testUpdateValidationError()
	{
		$this->expectException(ValidationException::class);

		$request = new UserProfileUpdateRequest();
		$request->id = "";
		$request->name = "";

		$this->userService->updateProfile($request);
	}

	public function testUpdateNotFound()
	{
		$this->expectException(ValidationException::class);
		$request = new UserProfileUpdateRequest();
		$request->id = "eko";
		$request->name = "Budi";

		$this->userService->updateProfile($request);

	}
}