<?php
class SecurityManager extends BaseManager {




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