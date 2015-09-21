<?php



class BaseService {
	/**
	 * 
	 * @var User
	 */
	protected $contextUser;

	protected $repository;
	
	/**
	 * security manager
	 * @var SecurityManager
	 */
	protected $securityManager;

	public $config;
	
	function __construct($contextUser, $repository) {
		$this->contextUser = $contextUser;
		$this->repository = $repository;
		$this->securityManager = new SecurityManager($repository);
	}
	
	/**
	 * logs an action and rewards points accordingly
	 * @param string $actionName
	 * @param object $object
	 * @return int Points for this action
	 */
	public function logActionByName($actionName, $object) {
		$action = $this->repository->getActionByName($actionName);
		return $this->logAction($action, $object);
	}

	/**
	 * logs an action and rewards points accordingly
	 * @param Action $action
	 * @param object $object
	 * @return int Points for this action
	 */
	public function logAction($action, $object) {
		if ($action === null) {
			throw new Exception("logAction: action must not be null");
		}
		if ($object === null) {
			throw new Exception("logAction: object must not be null");
		}
		if (get_class($action) != "Action") {
			throw new ParameterException("action is not of type Action");
		}
		$actionLogItem = new ActionLogItem();
		$actionLogItem->actionId = $action->actionId;
		$actionLogItem->objectId = $object->getId();
		$actionLogItem->objectTypeId = $action->objectTypeId;
		$actionLogItem->points = $action->points;
		$actionLogItem->timestamp = time();
		$actionLogItem->userId = $this->contextUser->userId;

		return $this->logActionLogItem($actionLogItem);
	}
	
	/**
	 * logs an actionlogitem and rewards points accordingly
	 * @param ActionLogItem $actionLogItem
	 * @param object $object
	 * @return int Points for this action
	 */
	public function logActionLogItem($actionLogItem) {
		if ($actionLogItem === null) {
			throw new Exception("actionlogitem must not be null");
		}
		$validationState = new ValidationState();
		$actionLogItem = $this->repository->createActionLogItem($actionLogItem, $validationState);
		$this->repository->addUserPoints($actionLogItem->userId, $actionLogItem->points);
		return $actionLogItem->points;
	}
	
	/**
	 * checks if an object has been found
	 * raises notfoundexception
	 */
	public function checkIsFound($object) {
		if (is_null($object)) {
			throw new NotFoundException("Object not found");
		}
	}
	
}


?>