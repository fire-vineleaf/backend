<?php
class SecurityManager extends BaseManager {

	public function getPlayerById($id) {
		return $this->repository->getPlayerById($id);
	}

	/**
	 * creates a new player, including the account
	 */
	public function createPlayer($email, $name, $password) {
		$player =new Player();
		$player->name = $name;
		$player->points = 0;
		$player->p3 = 0;
		$player->rights = 0;
		$player = $this->repository->createPlayer($player);
		
		$account = new Account();
		$account->email = $email;
		$account->password = $password;
		$account->createdAt = time();
		$account->playerId = $player->playerId;
		$account = $this->repository->createAccount($account);
		return $player;
	}

	/**
	 * gets account by name
	 * @param string $name
	 * @return Account
	 */
	public function getAccountByEmail($email) {
		if ($email == "") {
			throw new ParameterException("email is empty");
		}
		$model = $this->repository->getAccountByEmail($email);
		if (($model == null) || ($model === false)) {
			throw new NotFoundException($email);
		}
		return $model;
	}

}

?>