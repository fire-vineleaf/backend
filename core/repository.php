<?php

class CouchRepository {
	public $cb;
	
	function __construct($host) {
		$this->cb = new Couchbase($host);
	}
	
	public function getUserByName($name) {
		$json = $this->cb->get("acc_$name");

		$user = User::CreateModelFromJson($json);
		return $user;
	}

	public function getUserById($id) {
		$json = $this->cb->get("p_$id");

		$user = User::CreateModelFromJson($json);
		return $user;
	}

	
}

/**
 * MySQL Repository Implementation
 *
 */
class MySqlRepository {

	public $mysqli;

	function __construct($host, $userName, $password, $database) {
		$this->mysqli = new mysqli($host, $userName, $password, $database);
	}

	/**
	 * opens a new transaction
	 */
	public function beginTransaction() {
		$this->mysqli->autocommit(FALSE);
	}
	
	/**
	 * commits current transaction
	 */
	public function commit() {
		$this->mysqli->commit();
		$this->mysqli->autocommit(TRUE);
	}
	
	private function getUser($stmt) {
		$stmt->execute();
		$a = array();
		$stmt->bind_result($a["userId"], $a["name"], $a["createdAt"], $a["password"], $a["created_by_user_id"], $a["displayName"], $a["email"], $a["points"]);
		if ($stmt->fetch()) {
			return User::CreateModelFromRepositoryArray($a);
		} else {
			return null;		
		}	
	}

	/**
	 * gets User by name
	 * @param string $name
	 * @return User
	 */
	public function getUserByName($name) {
		$query = "SELECT user_id, name, created_at, password, display_name, email, points, is_confirmed, confirmation_key, clan_id, rights FROM users where name = ?";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("s", $name);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		$a = array();
		$rc = $stmt->bind_result($a["userId"], $a["name"], $a["createdAt"], $a["password"], $a["displayName"], $a["email"], $a["points"], $a["isConfirmed"], $a["confirmationKey"], $a["clanId"], $a["rights"]);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
	
		if ($stmt->fetch()) {
			return User::CreateModelFromRepositoryArray($a);
		} else {
			return null;
		}
	}

/**
 * retrieves all Buildings
 * @return Array
 */
public function getCampBuildings($campId) {
	$query = "SELECT building_id, camp_id, type, level FROM buildings WHERE camp_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $campId);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["buildingId"], $a["campId"], $a["type"], $a["level"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Building::CreateModelFromRepositoryArray($a);
	}
	return $models;
}

/**
 * get Building by id
 * @param int $id
 * @return Building 
 */	
public function getBuildingById($id) {
	$query = "SELECT building_id, camp_id, type, level FROM buildings where building_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["buildingId"], $a["campId"], $a["type"], $a["level"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Building::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}

/**
 * retrieves all Camps
 * @return Array
 */
public function getUserCamps($userId) {
	$query = "SELECT camp_id, name, user_id, x, y, b1, b2, b3, p1, p2, scores FROM camps WHERE user_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $userId);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["campId"], $a["name"], $a["userId"], $a["x"], $a["y"], $a["b1"], $a["b2"], $a["b3"], $a["p1"], $a["p2"], $a["scores"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Camp::CreateModelFromRepositoryArray($a);
	}
	return $models;
}

/**
 * get Camp by id
 * @param int $id
 * @return Camp 
 */	
public function getCampById($id) {
	$query = "SELECT camp_id, name, user_id, x, y, b1, b2, b3, p1, p2, scores FROM camps where camp_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["campId"], $a["name"], $a["userId"], $a["x"], $a["y"], $a["b1"], $a["b2"], $a["b3"], $a["p1"], $a["p2"], $a["scores"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Camp::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}

public function getPreviousBuildingTask($buildingId) {
	$query = "SELECT t.task_id, t.finished_at, t.object_id1, t.object_id2, t.type, t.level FROM tasks t, buildings b where t.object_id2 = b.building_id and b.building_id = ? and t.type = ? ORDER BY finished_at DESC, t.task_id DESC";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$taskType = TaskTypes::UpgradeBuilding;
	$rc = $stmt->bind_param("ii", $buildingId, $taskType);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["taskId"], $a["finishedAt"], $a["objectId1"], $a["objectId2"], $a["type"], $a["level"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Task::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}

public function getLastCampTask($campId) {
	$query = "SELECT t.task_id, t.finished_at, t.object_id1, t.object_id2, t.type, t.level FROM tasks t where t.object_id1 = ? and t.type = ? ORDER BY finished_at DESC";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$taskType = TaskTypes::UpgradeBuilding;
	$rc = $stmt->bind_param("ii", $campId, $taskType);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["taskId"], $a["finishedAt"], $a["objectId1"], $a["objectId2"], $a["type"], $a["level"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Task::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}



/**
 * updates Camp 
 * @param Camp $model
 * @return Camp 
 */
public function payCamp($model) {
	$query = "UPDATE camps SET b1 = ?, b2 = ?, b3 = ?, p1 = ?, p2 = ? WHERE camp_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iiiiii"
		, $model->b1
		, $model->b2
		, $model->b3
		, $model->p1
		, $model->p2
		, $model->campId	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}

/**
 * creates Task 
 * @param Task $model
 * @return Task 
 */	
public function createTask($model) {
	$query = "INSERT INTO tasks (finished_at, object_id1, object_id2, type, level) VALUES ( ?, ?, ?, ?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iiiii"
		, $model->finishedAt
, $model->objectId1
, $model->objectId2
, $model->type
, $model->level
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->taskId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}


/**
 * retrieves all Tasks
 * @return Array
 */
public function getDueTasks() {
	$query = "SELECT task_id, finished_at, object_id1, object_id2, type, level FROM tasks WHERE finished_at <= ? ORDER BY finished_at";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$t = time();
	$rc = $stmt->bind_param("i", $t);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["taskId"], $a["finishedAt"], $a["objectId1"], $a["objectId2"], $a["type"], $a["level"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Task::CreateModelFromRepositoryArray($a);
	}
	return $models;
}

/**
 * updates Building 
 * @param Building $model
 * @return Building 
 */
public function updateBuildingLevel($model) {
	$query = "UPDATE buildings SET level = ? WHERE building_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("ii"
		, $model->level
		, $model->buildingId
		);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	
	return $model;
}

/**
 * deletes Task 
 * @param int $id
 */	
public function deleteTask($id) {
	$query = "DELETE FROM tasks WHERE task_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}

/**
 * retrieves all Tasks
 * @return Array
 */
public function getQueue($type, $objectId1, $objectId2) {
	$query = "SELECT task_id, finished_at, object_id1, object_id2, type, level FROM tasks WHERE type = ? and object_id1 = ?";
	if (!is_null($objectId2)) {
		$query .= " and object_id2 = ?";
	}
	$query .= " ORDER BY finished_at";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}

	if (is_null($objectId2)) {
		$rc = $stmt->bind_param("ii"
			, $type
			, $objectId1
		);
	} else {
		$rc = $stmt->bind_param("iii"
			, $type
			, $objectId1
			, $objectId2
		);
	}


	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["taskId"], $a["finishedAt"], $a["objectId1"], $a["objectId2"], $a["type"], $a["level"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Task::CreateModelFromRepositoryArray($a);
	}
	return $models;
}


/**
 * creates Invitation 
 * @param Invitation $model
 * @return Invitation 
 */	
public function createInvitation($model) {
	$query = "INSERT INTO invitations (created_by, user_id, created_at, clan_id, type) VALUES ( ?, ?, ?, ?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iiiii"
		, $model->createdBy
, $model->userId
, $model->createdAt
, $model->clanId
, $model->type
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->invitationId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}
	
/**
 * get Invitation by id
 * @param int $id
 * @return Invitation 
 */	
public function getInvitationById($id) {
	$query = "SELECT invitation_id, created_by, user_id, created_at, clan_id, type FROM invitations where invitation_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["invitationId"], $a["createdBy"], $a["userId"], $a["createdAt"], $a["clanId"], $a["type"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Invitation::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}
	
/**
 * get User by id
 * @param int $id
 * @return User 
 */	
public function getUserById($id) {
	$query = "SELECT user_id, name, created_at, password, display_name, email, points, is_confirmed, confirmation_key, clan_id, rights FROM users where user_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["userId"], $a["name"], $a["createdAt"], $a["password"], $a["displayName"], $a["email"], $a["points"], $a["isConfirmed"], $a["confirmationKey"], $a["clanId"], $a["rights"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return User::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}

	/**
 * creates Participant 
 * @param Participant $model
 * @return Participant 
 */	
public function createParticipant($model) {
	$query = "INSERT INTO participants (user_id, message_id) VALUES (?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("ii"
		, $model->userId
		, $model->messageId
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->messageId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}

/**
 * creates Reply 
 * @param Reply $model
 * @return Reply 
 */	
public function createReply($model) {
	$query = "INSERT INTO replies (reply, created_at, created_by, message_id) VALUES ( ?, ?, ?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("siii"
		, $model->reply
, $model->createdAt
, $model->createdBy
, $model->messageId
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->replyId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}

/**
 * creates Message 
 * @param Message $model
 * @return Message 
 */	
public function createMessage($model) {
	$query = "INSERT INTO messages (created_at, created_by, subject) VALUES ( ?, ?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iis"
		, $model->createdAt
, $model->createdBy
, $model->subject
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->messageId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}

/**
 * deletes Invitation 
 * @param int $id
 */	
public function deleteInvitation($id) {
	$query = "DELETE FROM invitations WHERE invitation_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}

	public function getNumParticipants($messageId) {
		$stmt = $this->mysqli->prepare("SELECT count(user_id) as numParticipants FROM participants where message_id = ?");
		$stmt->bind_param("i", $messageId);
		$stmt->execute();
		$a = array();
		$stmt->bind_result($a["numParticipants"]);
		if ($stmt->fetch()) {
			return $a["numParticipants"];
		} else {
			return 0;		
		}	
	}
/**
 * deletes Reply 
 * @param int $id
 */	
public function deleteReply($id) {
	$query = "DELETE FROM replies WHERE reply_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}

	/**
	 * deletes all replies of a message
	 * @param int $id
	 */	
	public function deleteMessageReplies($id) {
		$query = "DELETE FROM replies WHERE message_id = ?";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("i", $id);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
	}

	/**
 * deletes Message 
 * @param int $id
 */	
public function deleteMessage($id) {
	$query = "DELETE FROM messages WHERE message_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}

/**
 * retrieves all Messages of a user
 * @return Array
 */
public function getMessages($userId) {
	$query = "SELECT p.message_id, m.created_at, m.created_by, m.subject, p.is_read, u.user_id, u.name, u.points FROM messages m, participants p, users u WHERE u.user_id = m.created_by and m.message_id = p.message_id and p.user_id = ? order by created_at";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $userId);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["messageId"], $a["createdAt"], $a["createdBy"], $a["subject"], $a["isRead"], $a["userId"], $a["displayName"], $a["scores"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$message =  Message::CreateModelFromRepositoryArray($a);
		$message->createdByUser = UserInfo::CreateModelFromRepositoryArray($a);
		$models[] = $message;
	}
	return $models;
}

/**
 * retrieves all Replys
 * @param int $messageId
 * @return Array
 */
public function getReplies($messageId) {
	$query = "SELECT reply_id, reply, created_at, created_by, message_id FROM replies WHERE message_id = ? ORDER BY created_at";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $messageId);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["replyId"], $a["reply"], $a["createdAt"], $a["createdBy"], $a["messageId"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Reply::CreateModelFromRepositoryArray($a);
	}
	return $models;
}

/**
 * creates FeedItem 
 * @param FeedItem $model
 * @return FeedItem 
 */	
public function createFeedItem($model) {
	$query = "INSERT INTO feed_items (user_id, clan_id, type, created_at, created_by, payload) VALUES ( ?, ?, ?, ?, ?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iiiiis"
		, $model->userId
, $model->clanId
, $model->type
, $model->createdAt
, $model->createdBy
, $model->payload
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->feedItemId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}

/**
 * get Message by id
 * @param int $id
 * @return Message 
 */	
public function getMessageById($id) {
	$query = "SELECT message_id, created_at, created_by, subject FROM messages where message_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["messageId"], $a["createdAt"], $a["createdBy"], $a["subject"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Message::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}

/**
 * updates Participant 
 * @param Participant $model
 * @return Participant 
 */
public function updateParticipant($model) {
	$query = "UPDATE participants SET is_read = ? WHERE message_id = ? and user_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iii"
		, $model->isRead
, $model->messageId
 
		, $model->userId	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	
	return $model;
}

/**
 * retrieves all Participants
 * @return Array
 */
public function getParticipants($messageId) {
	$query = "SELECT message_id, user_id, is_read FROM participants where message_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i"
		, $model->messageId	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["messageId"], $a["userId"], $a["isRead"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Participant::CreateModelFromRepositoryArray($a);
	}
	return $models;
}


/**
 * deletes Participant 
 * @param int $id
 */	
public function deleteParticipant($userId, $messageId) {
	$query = "DELETE FROM participants WHERE message_id = ? and user_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("ii", $messageId, $userId);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}
	

	/**
	 * retrieves number of points of user
	 * @param unknown $userId
	 * @return int
	 */
	public function getUserTotalPointsById($userId) {
		$stmt = $this->mysqli->prepare("SELECT points FROM users where user_id = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$a = array();
		$stmt->bind_result($a["points"]);
		if ($stmt->fetch()) {
			return $a["points"];
		} else {
			return null;		
		}	
	}
		

	
	/**
	 * retrieves all Users
	 * @return Array
	 */
	public function getUsers() {
		$query = "SELECT user_id, name, wiki_name, created_at, password, display_name, email, points, is_confirmed, confirmation_key, created_by_user_id, home_dashboard_id FROM users";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		$a = array();
		$rc = $stmt->bind_result($a["userId"], $a["name"], $a["wikiName"], $a["createdAt"], $a["password"], $a["displayName"], $a["email"], $a["points"], $a["isConfirmed"], $a["confirmationKey"], $a["createdByUserId"], $a["homeDashboardId"]);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		$models = array();
		while ($stmt->fetch()) {
			$models[] = User::CreateModelFromRepositoryArray($a);
		}
		return $models;
	}
		
	
	/**
 * creates Camp 
 * @param Camp $model
 * @return Camp 
 */	
public function createCamp($model) {
	$query = "INSERT INTO camps (name, user_id, x, y) VALUES ( ?, ?, ?, ?)";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("siii"
		, $model->name
, $model->userId
, $model->x
, $model->y
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->execute()) {
		$model->campId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}
	
	/**
	 * creates Building 
	 * @param Building $model
	 * @return Building 
	 */	
	public function createBuilding($model) {
		$query = "INSERT INTO buildings (camp_id, type, level) VALUES (?, ?, ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("iii"
			, $model->campId
			, $model->type
			, $model->level
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->buildingId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}

/**
 * retrieves all Fields
 * @return Array
 */
public function getSection($x1, $y1, $x2, $y2) {
	$query = "SELECT field_id, type, x, y, object_id FROM fields WHERE x >= ? and x <= ? and y >= ? and y <= ? order by y, x";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
		$rc = $stmt->bind_param("iiii"
			, $x1
			, $x2
			, $y1
			, $y2
		);
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($fieldId, $type, $x, $y, $objectId);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$field = new Field();
		$field->fieldId = $fieldId;
		$field->x = $x;
		$field->y = $y;
		$field->type = $type;
		$field->objectId = $objectId;
		$models[] = $field;
	}
	return $models;
}

public function getFieldByXY($x, $y) {
	$query = "SELECT field_id, type, x, y, object_id FROM fields where x = ? AND y = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("ii", $x, $y);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["fieldId"], $a["type"], $a["x"], $a["y"], $a["objectId"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Field::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}
/**
 * get Field by id
 * @param int $id
 * @return Field 
 */	
public function getFieldById($id) {
	$query = "SELECT field_id, type, x, y, object_id FROM fields where field_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["fieldId"], $a["type"], $a["x"], $a["y"], $a["objectId"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if ($stmt->fetch()) {
		return Field::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}
/**
 * updates Field 
 * @param Field $model
 * @return Field 
 */
public function updateField($model) {
	$query = "UPDATE fields SET type = ?, x = ?, y = ?, object_id = ? WHERE field_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("iiiii"
		, $model->type
, $model->x
, $model->y
, $model->objectId
 
		, $model->fieldId	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	
	return $model;
}
public function getNumFields() {
	$query = "SELECT COUNT(*) FROM fields";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$n = 0;
	$rc = $stmt->bind_result($n);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	
	if ($stmt->fetch()) {
		return $n;
	} else {
		return 0;
	}
}

	/**
	 * creates Field 
	 * @param Field $model
	 * @return Field 
	 */	
	public function createField($model) {
		$query = "INSERT INTO fields (type, x, y, object_id) VALUES ( ?, ?, ?, ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("iiii"
			, $model->type
	, $model->x
	, $model->y
	, $model->objectId
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->fieldId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}
	
	/**
	 * creates Clan 
	 * @param Clan $model
	 * @return Clan 
	 */	
	public function createClan($model) {
		$query = "INSERT INTO clans (name) VALUES ( ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("s"
			, $model->name
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->clanId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}
	
	
	
	
	/**
	 * creates User
	 * @param User $model
	 * @return User
	 */
	public function createUser($model) {
		$query = "INSERT INTO users (name, created_at, password, display_name, email, points, is_confirmed, confirmation_key, clan_id, rights) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("sisssiisii"
				, $model->name
				, $model->createdAt
				, $model->password
				, $model->displayName
				, $model->email
				, $model->points
				, $model->isConfirmed
				, $model->confirmationKey
				, $model->clanId
				, $model->rights
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->userId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}

	/**
	 * updates User 
	 * @param User $model
	 * @return User 
	 */
	public function updateUserClan($model) {
		$query = "UPDATE users SET clan_id = ? WHERE user_id = ?";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("ii"
			, $model->clanId
	 
			, $model->userId	);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}
/**
 * deletes Clan 
 * @param int $id
 */	
public function deleteClan($id) {
	$query = "DELETE FROM clans WHERE clan_id = ?";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i", $id);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}
	/**
	 * updates User 
	 * @param User $model
	 * @return User 
	 */
	public function updateUserRights($model) {
		$query = "UPDATE users SET rights = ? WHERE user_id = ?";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("ii"
			, $model->rights
	 
			, $model->userId	);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}
	
	
	/**
	 * creates Thread 
	 * @param Thread $model
	 * @return Thread 
	 */	
	public function createThread($model) {
		$query = "INSERT INTO threads (subject, clan_id, created_at, created_by) VALUES ( ?, ?, ?, ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("siii"
			, $model->subject
	, $model->clanId
	, $model->createdAt
	, $model->createdBy
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->threadId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}	

	/**
	 * creates Post 
	 * @param Post $model
	 * @return Post 
	 */	
	public function createPost($model) {
		$query = "INSERT INTO posts (thread_id, created_at, created_by, content) VALUES ( ?, ?, ?, ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("iiis"
			, $model->threadId
	, $model->createdAt
	, $model->createdBy
	, $model->content
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->postId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}
		
public function getClanMembers($clanId) {
	$query = "SELECT user_id, name, display_name, points FROM users where clan_id = ? order by points";
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	$rc = $stmt->bind_param("i"
		, $clanId
	);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$a = array();
	$rc = $stmt->bind_result($a["userId"], $a["name"], $a["displayName"], $a["points"]);
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	$models = array();
	while ($stmt->fetch()) {
		$models[] = User::CreateModelFromRepositoryArray($a);
	}
	return $models;
}
	
	
	
	/**
	 * confirms a user
	 * @param User $model
	 * @return User
	 */
	public function confirmUser($model) {
		$query = "UPDATE users SET is_confirmed = 1 WHERE user_id = ?";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("i"
				, $model->userId
				);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
	
		return $model;
	}
	
	/**
	 * adds points to user
	 * @param User $user
	 * @param points $points
	 */
	public function addUserPoints($userId, $points) {
		$query = "UPDATE users set points = points + ? where user_id = ?";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("ii",
				$points, $userId
			);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if (!$stmt->execute()) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
	}
	
	/**
	 * creates Parameter
	 * @param Parameter $model
	 * @return Parameter
	 */
	public function createParameter($model) {
	
		$query = "INSERT INTO parameters (name, type, default_value, object_type_id) VALUES (?, ?, ?, ?)";
		$stmt = $this->mysqli->prepare($query);
		if ($stmt === false) {
			throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
		}
		$rc = $stmt->bind_param("sisi"
				, $model->name
				, $model->type
				, $model->defaultValue
				, $model->objectTypeId
		);
		if ($rc === false) {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		if ($stmt->execute()) {
			$model->parameterId = $this->mysqli->insert_id;
		} else {
			throw new RepositoryException($stmt->error, $stmt->errno);
		}
		return $model;
	}


	
}

?>