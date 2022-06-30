<?php 

namespace Als\Belajar\PHP\MVC\Controller;

use Als\Belajar\PHP\MVC\App\View;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Exception\ValidationException;
use Als\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Als\Belajar\PHP\MVC\Model\UserLoginRequest;
use Als\Belajar\PHP\MVC\Repository\UserRepository;
use Als\Belajar\PHP\MVC\Repository\SessionRepository;
use Als\Belajar\PHP\MVC\Service\UserService;
use Als\Belajar\PHP\MVC\Service\SessionService;
use Als\Belajar\PHP\MVC\View\redirect;

class UserController
{
	private UserService $userService;
	private SessionService $sessionService;

	public function __construct()
	{
		$connection = Database::getConnection();
		$userRepository = new UserRepository($connection);
		$this->userService = new UserService($userRepository);

		$sessionRepository = new SessionRepository($connection);
		$this->sessionService = new SessionService($sessionRepository, $userRepository);

	}


	public function register()
	{
		View::render('User/register', [
			'title' => 'Register new User'
		]);
	}

	public function postRegister()
	{
		$request = new UserRegisterRequest();
		$request->id = $_POST['id'];
		$request->name = $_POST['name'];
		$request->password = $_POST['password'];

		try {
			$this->userService->register($request);
			// Redirect to /users/login
			View::redirect('/users/login');
		} catch (ValidationException $exception) {
			View::render('User/register', [
				'title' => 'Register new User',
				'error' => $exception->getMessage()
			]);
		}
	}

	public function login()
	{
		View::render('User/login', [
			'title' => 'Login user'
		]);
	}

	public function postLogin()
	{


		$request = new UserLoginRequest();
		$request->id = $_POST['id'];
		$request->password = $_POST['password'];

		try {
			$response = $this->userService->login($request);
			$this->sessionService->create($response->user->id);
			View::redirect('/');
		} catch (ValidationException $exception) {
			View::render('User/login', [
				'title' => 'Login User',
				'error' => $exception->getMessage()
			]);
		}
		
	}
}