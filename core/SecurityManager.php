<?php
class SecurityManager extends BaseManager {

	public function getPlayerById($id) {
		return $this->repository->getPlayerById($id);
	}

	/**
	 * creates a new player, including the account
	 */
	public function createPlayer($email, $name, $password) {
		$player = $this->repository->getNextFreePlayer();
		if (is_null($player)) {
			throw new Exception("no more free players available");
		}
		$player->name = $name;
		$player->points = 0;
		$player->p3 = 0;
		$player->rights = 0;
		$player->isFree = false;
		$player = $this->repository->updatePlayer($player);
		
		$account = new Account();
		$account->email = $email;
		$account->password = $password;
		$account->createdAt = time();
		$account->playerId = $player->playerId;
		$account = $this->repository->createAccount($account);
		
		// rename camp
		$camp = $this->repository->getPlayerCamp($player->playerId);
		$camp->name = $name;
		$this->repository->updateCampName($camp);
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