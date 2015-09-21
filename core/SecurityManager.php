<?php
class SecurityManager extends BaseManager {




	/**
	 * gets user by name
	 * @param string $name
	 * @return User
	 */
	public function getUserByName($name) {
		if ($name == "") {
			throw new ParameterException("name is empty");
		}
		$model = $this->repository->getUserByName($name);
		if (($model == null) || ($model === false)) {
			throw new NotFoundException($name);
		}
		return $model;
	}

}

?>