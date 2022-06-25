<?php 

namespace Als\Belajar\PHP\MVC\Repository;

use Als\Belajar\PHP\MVC\Domain\User;

class UserRepository
{

	private \PDO $connection;

	public function __construct(\PDO $connection)
	{
		$this->connection = $connection;
	}

	public function save(User $user): User
	{	
		$sql = "INSERT INTO users(id, name, password) VALUES(?, ?, ?)"
		$statement = $this->connection->prepare($sql);
		$statement->execute([
			$user->id, $user->name, $user->password
		]);
		return $user;
	}

}