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

	
private function prepare($query) {
	$stmt = $this->mysqli->prepare($query);
	if ($stmt === false) {
		throw new RepositoryException($this->mysqli->error, $this->mysqli->errno);
	}
	return $stmt;
}
private function checkBind($rc) {
	if ($rc === false) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
}
private function execute($stmt) {
	if (!$stmt->execute()) {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $stmt;
}
/**
 * gets Account by name
 * @param string $name
 * @return Account 
 */	
public function getAccountByEmail($email) {
	$query = "SELECT account_id, player_id, created_at, password, email FROM accounts where email = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("s", $email);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["accountId"], $a["playerId"], $a["createdAt"], $a["password"], $a["email"]);
	$this->checkBind($rc);
	if ($stmt->fetch()) {
		return Account::CreateModelFromRepositoryArray($a);
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["buildingId"], $a["campId"], $a["type"], $a["level"]);
	$this->checkBind($rc);
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
public function getPlayerCamps($playerId) {
	$query = "SELECT camp_id, name, player_id, x, y, b1, b2, b3, p1, p2, scores FROM camps WHERE player_id";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $playerId);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["campId"], $a["name"], $a["playerId"], $a["x"], $a["y"], $a["b1"], $a["b2"], $a["b3"], $a["p1"], $a["p2"], $a["scores"]);
	$this->checkBind($rc);
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
	$query = "SELECT camp_id, name, player_id, x, y, b1, b2, b3, p1, p2, scores FROM camps where camp_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["campId"], $a["name"], $a["playerId"], $a["x"], $a["y"], $a["b1"], $a["b2"], $a["b3"], $a["p1"], $a["p2"], $a["scores"]);
	$this->checkBind($rc);
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiiii"
		, $model->finishedAt
, $model->objectId1
, $model->objectId2
, $model->type
, $model->level
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->taskId = $this->mysqli->insert_id;
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("ii"
		, $model->level
		, $model->buildingId
		);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}

/**
 * deletes Task 
 * @param int $id
 */	
public function deleteTask($id) {
	$query = "DELETE FROM tasks WHERE task_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
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
	$query = "INSERT INTO invitations (created_by, player_id, created_at, clan_id, type) VALUES ( ?, ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiiii"
		, $model->createdBy
, $model->playerId
, $model->createdAt
, $model->clanId
, $model->type
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->invitationId = $this->mysqli->insert_id;
	return $model;
}
	
/**
 * get Invitation by id
 * @param int $id
 * @return Invitation 
 */	
public function getInvitationById($id) {
	$query = "SELECT invitation_id, created_by, player_id, created_at, clan_id, type FROM invitations where invitation_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["invitationId"], $a["createdBy"], $a["playerId"], $a["createdAt"], $a["clanId"], $a["type"]);
	$this->checkBind($rc);
	if ($stmt->fetch()) {
		return Invitation::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}
	
/**
 * get Player by id
 * @param int $id
 * @return Player 
 */	
public function getPlayerById($id) {
	$query = "SELECT player_id, name, points, clan_id, rights, p3 FROM players where player_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["playerId"], $a["name"], $a["points"], $a["clanId"], $a["rights"], $a["p3"]);
	$this->checkBind($rc);
	if ($stmt->fetch()) {
		return Player::CreateModelFromRepositoryArray($a);
	} else {
		return null;
	}
}

/**
 * retrieves all Invitations
 * @return Array
 */
public function getPlayerInvitations($id, $type) {
	$query = "SELECT i.invitation_id, i.created_by, i.player_id, i.created_at, i.clan_id, i.type, c.name FROM invitations i, clans c WHERE i.clan_id = c.clan_id AND i.player_id = ? and type = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("ii", $id, $type);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["invitationId"], $a["createdBy"], $a["playerId"], $a["createdAt"], $a["clanId"], $a["type"], $a["name"]);
	$this->checkBind($rc);
	$models = array();
	while ($stmt->fetch()) {
		$i = Invitation::CreateModelFromRepositoryArray($a);
		$i->clan = Clan::CreateModelFromRepositoryArray($a);
		$models[]  = $i;
	}
	return $models;
}

/**
 * get Clan by id
 * @param int $id
 * @return Clan 
 */	
public function getClanById($id) {
	$query = "SELECT clan_id, name FROM clans where clan_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["clanId"], $a["name"]);
	$this->checkBind($rc);
	if ($stmt->fetch()) {
		return Clan::CreateModelFromRepositoryArray($a);
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
	$query = "INSERT INTO participants (player_id, message_id, is_read) VALUES (?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iii"
		, $model->playerId
		, $model->messageId
		, $model->isRead
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->messageId = $this->mysqli->insert_id;
	return $model;
}

/**
 * creates Reply 
 * @param Reply $model
 * @return Reply 
 */	
public function createReply($model) {
	$query = "INSERT INTO replies (reply, created_at, created_by, message_id) VALUES ( ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("siii"
		, $model->reply
, $model->createdAt
, $model->createdBy
, $model->messageId
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->replyId = $this->mysqli->insert_id;
	return $model;
}

/**
 * creates Message 
 * @param Message $model
 * @return Message 
 */	
public function createMessage($model) {
	$query = "INSERT INTO messages (created_at, created_by, subject) VALUES ( ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iis"
		, $model->createdAt
, $model->createdBy
, $model->subject
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->messageId = $this->mysqli->insert_id;
	return $model;
}

/**
 * deletes Invitation 
 * @param int $id
 */	
public function deleteInvitation($id) {
	$query = "DELETE FROM invitations WHERE invitation_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}

public function getNumParticipants($messageId) {
	$stmt = $this->mysqli->prepare("SELECT count(player_id) as numParticipants FROM participants where message_id = ?");
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}

/**
 * deletes Reply 
 * @param int $id
 */	
public function deleteMessageReplies($id) {
	$query = "DELETE FROM replies WHERE message_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}


/**
 * deletes Message 
 * @param int $id
 */	
public function deleteMessage($id) {
	$query = "DELETE FROM messages WHERE message_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}

/**
 * retrieves all Messages of a user
 * @return Array
 */
public function getMessages($playerId) {
	$query = "SELECT m.message_id, m.created_at, m.created_by, m.subject, p.is_read, pl.player_id, pl.name, pl.points FROM messages m, participants p, players pl WHERE pl.player_id = m.created_by and m.message_id = p.message_id and p.player_id = ? order by created_at";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $playerId);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["messageId"], $a["createdAt"], $a["createdBy"], $a["subject"], $a["isRead"], $a["playerId"], $a["name"], $a["points"]);
	$this->checkBind($rc);
	$models = array();
	while ($stmt->fetch()) {
		$message =  Message::CreateModelFromRepositoryArray($a);
		$message->createdByPlayer = PlayerInfo::CreateModelFromRepositoryArray($a);
		$models[] = $message;
	}
	return $models;
}

/**
 * retrieves all Replys
 * @return Array
 */
public function getReplies($messageId) {
	$query = "SELECT reply_id, reply, created_at, created_by, message_id FROM replies WHERE message_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $messageId);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["replyId"], $a["reply"], $a["createdAt"], $a["createdBy"], $a["messageId"]);
	$this->checkBind($rc);
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
	$query = "INSERT INTO feed_items (player_id, clan_id, type, created_at, created_by, payload) VALUES ( ?, ?, ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiiiii"
		, $model->playerId
, $model->clanId
, $model->type
, $model->createdAt
, $model->createdBy
, $model->payload
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->feedItemId = $this->mysqli->insert_id;
	return $model;
}

/**
 * get Message by id
 * @param int $id
 * @return Message 
 */	
public function getMessageById($id) {
	$query = "SELECT message_id, created_at, created_by, subject FROM messages where message_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["messageId"], $a["createdAt"], $a["createdBy"], $a["subject"]);
	$this->checkBind($rc);
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
	$query = "UPDATE participants SET is_read = ? WHERE message_id = ? and player_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iii"
		, $model->isRead
, $model->messageId 
		, $model->playerId	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	return $model;
}

/**
 * retrieves all Participants
 * @return Array
 */
public function getParticipants($messageId) {
	$query = "SELECT message_id, player_id, is_read FROM participants where message_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i"
		, $model->messageId	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["messageId"], $a["playerId"], $a["isRead"]);
	$this->checkBind($rc);
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
public function deleteParticipant($playerId, $messageId) {
	$query = "DELETE FROM participants WHERE message_id = ? and player_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("ii", $messageId, $playerId);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}
	
/**
 * creates Camp 
 * @param Camp $model
 * @return Camp 
 */	
public function createCamp($model) {
	$query = "INSERT INTO camps (name, player_id, x, y, b1, b2, b3, p1, p2, scores) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("siiiiiiiii"
		, $model->name
, $model->playerId
, $model->x
, $model->y
, $model->b1
, $model->b2
, $model->b3
, $model->p1
, $model->p2
, $model->scores
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->campId = $this->mysqli->insert_id;
	return $model;
}

	
/**
 * creates Building 
 * @param Building $model
 * @return Building 
 */	
public function createBuilding($model) {
	$query = "INSERT INTO buildings (camp_id, type, level) VALUES ( ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iii"
		, $model->campId
, $model->type
, $model->level
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->buildingId = $this->mysqli->insert_id;
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("ii", $x, $y);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["fieldId"], $a["type"], $a["x"], $a["y"], $a["objectId"]);
	$this->checkBind($rc);
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["fieldId"], $a["type"], $a["x"], $a["y"], $a["objectId"]);
	$this->checkBind($rc);
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiiii"
		, $model->type
, $model->x
, $model->y
, $model->objectId
 
		, $model->fieldId	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);	
	return $model;
}

public function getNumFields() {
	$query = "SELECT COUNT(*) FROM fields";
	$stmt = $this->prepare($query);
	$stmt = $this->execute($stmt);
	$n = 0;
	$rc = $stmt->bind_result($n);
	$this->checkBind($rc);
	
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiii"
		, $model->type
, $model->x
, $model->y
, $model->objectId
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->fieldId = $this->mysqli->insert_id;
	return $model;
}	
/**
 * creates Clan 
 * @param Clan $model
 * @return Clan 
 */	
public function createClan($model) {
	$query = "INSERT INTO clans (name) VALUES ( ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("s"
		, $model->name
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->clanId = $this->mysqli->insert_id;
	return $model;
}
	
/**
 * creates Account 
 * @param Account $model
 * @return Account 
 */	
public function createAccount($model) {
	$query = "INSERT INTO accounts (player_id, created_at, password, email) VALUES ( ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiss"
		, $model->playerId
, $model->createdAt
, $model->password
, $model->email
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->accountId = $this->mysqli->insert_id;
	return $model;
}

/**
 * creates Player 
 * @param Player $model
 * @return Player 
 */	
public function createPlayer($model) {
	$query = "INSERT INTO players (name, points, clan_id, rights, p3) VALUES ( ?, ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("siiii"
		, $model->name
, $model->points
, $model->clanId
, $model->rights
, $model->p3
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$model->playerId = $this->mysqli->insert_id;
	return $model;
}

/**
 * updates User 
 * @param User $model
 * @return User 
 */
public function updatePlayerClan($model) {
	$query = "UPDATE players SET clan_id = ? WHERE player_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("ii"
		, $model->clanId
		, $model->playerId	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	return $model;
}

/**
 * deletes Clan 
 * @param int $id
 */	
public function deleteClan($id) {
	$query = "DELETE FROM clans WHERE clan_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
}
	/**
	 * updates User 
	 * @param User $model
	 * @return User 
	 */
	public function updatePlayerRights($model) {
		$query = "UPDATE players SET rights = ? WHERE player_id = ?";
		$stmt = $this->prepare($query);
		$rc = $stmt->bind_param("ii"
			, $model->rights 
			, $model->playerId	);
		$this->checkBind($rc);
		$stmt = $this->execute($stmt);
		return $model;
	}

/**
 * retrieves all Threads of a clan
 * @return Array
 */
public function getThreads($id) {
	$query = "SELECT thread_id, subject, clan_id, created_at, created_by FROM threads WHERE clan_id = ?";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["threadId"], $a["subject"], $a["clanId"], $a["createdAt"], $a["createdBy"]);
	$this->checkBind($rc);
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Thread::CreateModelFromRepositoryArray($a);
	}
	return $models;
}	


/**
 * retrieves all Players
 * @return Array
 */
public function getLeaderboardPlayers() {
	$query = "SELECT player_id, name, points, clan_id, rights, p3 FROM players ORDER BY points DESC";
	$stmt = $this->prepare($query);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["playerId"], $a["name"], $a["points"], $a["clanId"], $a["rights"], $a["p3"]);
	$this->checkBind($rc);
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Player::CreateModelFromRepositoryArray($a);
	}
	return $models;
}

/**
 * retrieves all Posts
 * @return Array
 */
public function getPosts($id) {
	$query = "SELECT p.post_id, p.thread_id, p.created_at, p.created_by, p.content, pl.player_id, pl.name, pl.points FROM posts p, players pl WHERE p.created_by = pl.player_id AND thread_id = ? ORDER BY created_at";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i", $id);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["postId"], $a["threadId"], $a["createdAt"], $a["createdBy"], $a["content"], $a["playerId"], $a["name"], $a["points"]);
	$this->checkBind($rc);
	$models = array();
	while ($stmt->fetch()) {
		$m = Post::CreateModelFromRepositoryArray($a);
		$m->createdByPlayer = PlayerInfo::CreateModelFromRepositoryArray($a);
		$models[]  = $m;
	}
	return $models;
}

/**
 * creates Thread 
 * @param Thread $model
 * @return Thread 
 */	
public function createThread($model) {
	$query = "INSERT INTO threads (subject, clan_id, created_at, created_by) VALUES ( ?, ?, ?, ?)";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("siii"
		, $model->subject
, $model->clanId
, $model->createdAt
, $model->createdBy
	);
	$this->checkBind($rc);
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
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("iiis"
		, $model->threadId
, $model->createdAt
, $model->createdBy
, $model->content
	);
	$this->checkBind($rc);
	if ($stmt->execute()) {
		$model->postId = $this->mysqli->insert_id;
	} else {
		throw new RepositoryException($stmt->error, $stmt->errno);
	}
	return $model;
}
		
public function getClanMembers($clanId) {
	$query = "SELECT player_id, name, points FROM players where clan_id = ? order by points";
	$stmt = $this->prepare($query);
	$rc = $stmt->bind_param("i"
		, $clanId
	);
	$this->checkBind($rc);
	$stmt = $this->execute($stmt);
	$a = array();
	$rc = $stmt->bind_result($a["playerId"], $a["name"], $a["points"]);
	$this->checkBind($rc);
	$models = array();
	while ($stmt->fetch()) {
		$models[] = Player::CreateModelFromRepositoryArray($a);
	}
	return $models;
}
	
	
}

?>