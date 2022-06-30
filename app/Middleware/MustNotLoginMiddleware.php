<?php 

namespace Als\Belajar\PHP\MVC\Middleware;

use Als\Belajar\PHP\MVC\App\View;
use Als\Belajar\PHP\MVC\Config\Database;
use Als\Belajar\PHP\MVC\Repository\SessionRepository;
use Als\Belajar\PHP\MVC\Repository\UserRepository;
use Als\Belajar\PHP\MVC\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
	private SessionService $sessionService;

	public function __construct()
	{
		$sessionRepository = new SessionRepository(Database::getConnection());
		$userRepository = new UserRepository(Database::getConnection());
		$this->sessionService = new SessionService($sessionRepository, $userRepository);
	}

	function before(): void
	{
		$user = $this->sessionService->current();
		if($user != null) {
			View::redirect('/');
		}
	}
}