<?php

class BaseService {
	/**
	 * 
	 * @var Player
	 */
	protected $contextPlayer;
	/**
	 * 
	 * @var Account
	 */
	public $contextAccount;

	protected $repository;
	
	/**
	 * security manager
	 * @var SecurityManager
	 */
	protected $securityManager;

	public $config;
	
	function __construct($contextPlayer, $repository) {
		if (is_null($contextPlayer)) {
			throw new Exception("contextPlayer must not be null");
		}
		$this->contextPlayer = $contextPlayer;
		$this->repository = $repository;
		$this->securityManager = new SecurityManager($repository);
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

class ZSAService extends BaseService {

	public function getCamp($id) {
		$camp = $this->repository->getCampById($id);
		$camp->buildings = $this->repository->getCampBuildings($camp->campId);
		return $camp;
	}

	public function getCamps($id) {
		if (is_null($id)) {
			return $this->getPlayerCamps($this->contextPlayer->playerId);
		} else {
			return $this->getPlayerCamps($id);
		}
	}
	
	public function getClan($id) {
		if (is_null($id)) {
			// todo: check if clan
			return $this->repository->getClanById($this->contextPlayer->clanId, $this->contextPlayer->clanId);
		} else {
			return $this->repository->getClanById($id, $this->contextPlayer->clanId);
		}
	}

	private function getPlayerCamps($playerId) {
		$camps = $this->repository->getPlayerCamps($playerId);
		return $camps;
	}
	
	public function getPlayerInvitations() {
		$invitations = $this->repository->getPlayerInvitations($this->contextPlayer->playerId, 0);
		return $invitations;
	}
	
	public function getClanFeedItems() {
		$items = $this->repository->getClanFeedItems($this->contextPlayer->clanId);
		return $items;
	}
	
	public function getClanInvitations() {
		$invitations = $this->repository->getClanInvitations($this->contextPlayer->clanId, 0);
		return $invitations;
	}
	
	private function createCamp($camp) {
		$numBuildings = 13;
		$camp->points = $numBuildings;
		$camp = $this->repository->createCamp($camp);

		$campProperties = $camp->properties;
		for ($i=0;$i<$numBuildings;$i++) {
			$building = new Building();
			$building->type = $i;
			$building->campId = $camp->campId;
			$building->level = 1;
			$building = $this->repository->createBuilding($building);
			$campProperties = $this->updateCampProperties($campProperties, $building->type, $building->level);
		}
		$camp->properties = $campProperties;
		$camp = $this->repository->updateCampProperties($camp);
		return $camp;
	}
	
	
	
	
	/**
	 * Creates a new Field 
	 * @param Field $model
	 * @return Field 
	 */
	public function createField($field) {
		$field = $this->repository->createField($field);
		return $field;
	}
	
	/**
	 * Creates a new Clan 
	 * @param Clan $clan
	 * @return Clan 
	 */
	public function createClan($clan) {
		$clan = $this->repository->createClan($clan);
		$player = new Player();
		$player = $this->contextPlayer;
		$player->clanId = $clan->clanId;
		$player->rights = 
			Rights::_INVITE
			| Rights::_MASSMAIL
			| Rights::_MODERATOR
			| Rights::_DIPLOMACY
			| Rights::_DISMISS
			| Rights::_RIGHTS
			| Rights::_DISBAND;
		$player = $this->repository->updatePlayerClan($player);
		$player = $this->repository->updatePlayerRights($player);
			
		$item = $this->createFeedItem(FeedItemTypes::ClanCreated);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);

		return $clan;
	}
	
	public function leaveClan() {
		$this->removePlayerFromClan($this->contextPlayer);

		$item = $this->createFeedItem(FeedItemTypes::ClanLeft);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}
	
	public function disbandClan() {
		$clanId = $this->contextPlayer->clanId;
		$members = $this->repository->getClanMembers($clanId);
		foreach($members as $member) {
			$this->removePlayerFromClan($member);
		}
		$this->repository->deleteClan($clanId);
	}
	
	private function removePlayerFromClan($player) {
		$player->clanId = null;
		$player->rights = 0;
		$this->repository->updatePlayerClan($player);
		$this->repository->updatePlayerRights($player);
	}
	
	public function invitePlayer($playerId) {
		$invitation = new Invitation();
		$invitation->createdAt = time();
		$invitation->createdBy = $this->contextPlayer->playerId;
		$invitation->clanId = $this->contextPlayer->clanId;
		$invitation->playerId = $playerId;
		$invitation->type = InvitationTypes::Invitation;
		$invitation = $this->repository->createInvitation($invitation);
		$item = $this->createFeedItem(FeedItemTypes::InvitationSent);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
		return $invitation;
	}
	
	public function applyForClan($clanId) {
		$invitation = new Invitation();
		$invitation->createdAt = time();
		$invitation->createdBy = $this->contextPlayer->playerId;
		$invitation->clanId = $clanId;
		$invitation->playerId = $this->contextPlayer->playerId;
		$invitation->type = InvitationTypes::Application;
		$invitation = $this->repository->createInvitation($invitation);

		$item = $this->createFeedItem(FeedItemTypes::ApplicationSent);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
		return $invitation;
	}
	
	public function acceptInvitation($invitationId) {
		$invitation = $this->repository->getInvitationById($invitationId);
		$this->checkIsFound($invitation);
		$player = $this->repository->getPlayerById($invitation->playerId);
		$curClanId = $player->clanId;
		$player->clanId = $invitation->clanId;
		$player->rights = 0;
		$player = $this->repository->updatePlayerClan($player);
		$player = $this->repository->updatePlayerRights($player);

		$this->repository->deleteInvitation($invitationId);
		$item = $this->createFeedItem(FeedItemTypes::InvitationAccepted);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
		
		// check: currently in clan?
		if (!is_null($curClanId)) {
			$item = $this->createFeedItem(FeedItemTypes::ClanLeft);
			$item->clanId = $curClanId;
			$item->payload = "todo: payload"; // todo: payload
			$this->repository->createFeedItem($item);
		}
	}

	public function rejectInvitation($invitationId) {
		$this->repository->deleteInvitation($invitationId);
		$item = $this->createFeedItem(FeedItemTypes::InvitationRejected);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}

	public function grantRight($playerId, $right) {
		$player = $this->repository->getPlayerById($playerId);
		$this->checkIsFound($player);
		$player->rights |= $right->right;
		$player = $this->repository->updatePlayerRights($player);
		$item = $this->createFeedItem(FeedItemTypes::RightGranted);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}
	public function hasRight($rights, $right) {
		return $rights & $right;
	}
	public function revokeRight($playerId, $right) {
		$player = $this->repository->getPlayerById($playerId);
		$this->checkIsFound($player);
		$player->rights &= ~$right->right;
		$player = $this->repository->updatePlayerRights($player);
		$item = $this->createFeedItem(FeedItemTypes::RightRevoked);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}
	
	public function getClans() {
		$clans = $this->repository->getClans($this->contextPlayer->clanId);
		return $clans;
	}
	public function getClanDiplomacy() {
		$clans = $this->repository->getClanDiplomacy($this->contextPlayer->clanId);
		return $clans;
	}
	
	private function createFeedItem($type) {
		$item = new FeedItem();
		$item->createdAt = time();
		$item->createdBy = $this->contextPlayer->playerId;
		if ($type >=50) {
			$item->playerId = $this->contextPlayer->playerId;
		} else {
			$item->clanId = $this->contextPlayer->clanId;
		}
		$item->type = $type;
		$item->payload = "";
		return $item;
	}
	
	
	/**
	 * updates clan of a player
	 * @param Player $player
	 */
	public function updatePlayerClan($player) {
		$this->repository->updatePlayerClan($player);
	}

	/**
	 * Creates a new Thread
	 * @param Thread $thread
	 * @return Thread
	 */
	public function createThread($thread) {
		$thread->createdBy = $this->contextPlayer->playerId;
		$thread->createdAt = time();
		$thread->clanId = $this->contextPlayer->clanId;
		$thread = $this->repository->createThread($thread);
		$post = new Post();
		$post->threadId = $thread->threadId;
		$post->createdBy = $this->contextPlayer->playerId;
		$post->createdAt = time();
		$post->content = $thread->content;
		$post = $this->repository->createPost($post);
		return $thread;
	}

	public function createPost($threadId, $post) {
		$post->threadId = $threadId;
		$post->createdBy = $this->contextPlayer->playerId;
		$post->createdAt = time();
		$post = $this->repository->createPost($post);
		return $post;
	}

	public function createMessage($message) {
		$message->createdAt = time();
		$message->createdBy = $this->contextPlayer->playerId;
		$message = $this->repository->createMessage($message);
		// and don't forget to add yourself as participant!
		$message->participants[] = $this->contextPlayer->playerId;
		foreach($message->participants as $playerId) {
			$participant = new Participant();
			$participant->playerId = $playerId;
			$participant->messageId = $message->messageId;
			if ($playerId == $this->contextPlayer->playerId) {
				$participant->isRead = 1;
			} else {
				$participant->isRead = 0;
			}
			$this->repository->createParticipant($participant);
		}
		$reply = new Reply();
		$reply->createdAt = time();
		$reply->createdBy = $this->contextPlayer->playerId;
		$reply->reply = $message->content;
		$reply->messageId = $message->messageId;
		$reply = $this->repository->createReply($reply);
		return $message;
	}
	
	public function getClanMembers($id) {
		if (is_null($id)) {
			$members = $this->repository->getClanMembers($this->contextPlayer->clanId);
		} else {
			$members = $this->repository->getClanMembers($id);
		}
		return $members;
	}

	public function getThreads() {
		$threads = $this->repository->getThreads($this->contextPlayer->clanId);
		return $threads;
	}

	public function getPosts($id) {
		$threads = $this->repository->getPosts($id);
		return $threads;
	}
	
	public function getPlayers() {
		$players = $this->repository->getPlayers();
		return $players;
	}
	
	public function setDiplomacy($clanId, $status) {
		$clan1Id = $this->contextPlayer->clanId;
		$clan2Id = $clanId;
		if ($status == 0) {
			// 0 will not be saved explicitly
			// so delete
			$this->repository->deleteDiplomacy($clan1Id, $clan2Id);
		} else {
			$diplomacy = $this->repository->getDiplomacy($clan1Id, $clan2Id);
			if (is_null($diplomacy)) {
				$diplomacy = new Diplomacy();
				$diplomacy->clan1Id = $clan1Id;
				$diplomacy->clan2Id = $clan2Id;
				$diplomacy->status = $status;
				$diplomacy = $this->repository->createDiplomacy($diplomacy);
			} else {
				$diplomacy->status = $status;
				$diplomacy = $this->repository->updateDiplomacy($diplomacy);
			}
		}
		$item = $this->createFeedItem(FeedItemTypes::DiplomacyChanged);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
			
	}
	
	public function replyToMessage($id, $reply) {
		$reply->createdAt = time();
		$reply->createdBy = $this->contextPlayer->playerId;
		$reply->messageId = $id;
		$reply = $this->repository->createReply($reply);

		// mark message as unread for all participants
		$participants = $this->repository->getParticipants($id);
		foreach($participants as $participant) {
			$participant->isRead = 0;
			$this->repository->updateParticipant($participant);
		}
		return $reply;
	}
	
	public function deleteMessage($messageId) {
		$this->repository->deleteParticipant($this->contextPlayer->playerId, $messageId);
		// check if this was the last participant
		// if yes: delete message and all replies
		$numParticipants = $this->repository->getNumParticipants($messageId);
		if ($numParticipants == 0) {
			$this->repository->deleteMessage($messageId);
			$this->repository->deleteMessageReplies($messageId);
		}
	}
	
	public function getMessages() {
		$messages = $this->repository->getMessages($this->contextPlayer->playerId);
		return $messages;
	}
	
	public function getMessage($messageId) {
		$message = $this->repository->getMessageById($messageId);
		// mark message as read for this player
		$participant = new Participant();
		$participant->playerId = $this->contextPlayer->playerId;
		$participant->messageId = $messageId;
		$participant->isRead = 1;
		$this->repository->updateParticipant($participant);
		return $message;
	}

	public function getReplies($messageId) {
		$replies = $this->repository->getReplies($messageId);
		return $replies;
	}

	private function checkGameConfig() {
		if (is_null($this->config)) {
			throw new Exception("game config not set");
		}
	}
	
	public function queueUpgradeBuilding($buildingId) {
		$this->checkGameConfig();
		$building = $this->repository->getBuildingById($buildingId);
		$camp = $this->repository->getCampById($building->campId);
		// determine costs and duration
		$previousTask = $this->repository->getPreviousBuildingTask($buildingId);
		$lastTask = $this->repository->getLastCampTask($camp->campId);
		if (is_null($previousTask)) {
			$nextLevel = $building->level;
		} else {
			$nextLevel = $previousTask->level;
		}
		$nextLevel++;
		if (is_null($lastTask)) {
			$finishTime = time();
		} else {
			$finishTime = $lastTask->finishedAt > time() ? $lastTask->finishedAt : time();
		}
		if (!isset($this->config["buildings"][$building->type][$nextLevel])) {
			var_dump($nextLevel);
			var_dump($building);
			var_dump($this->config["buildings"][$building->type]);

			throw new Exception("upgrade not possible");
		}
		$config = $this->config["buildings"][$building->type][$nextLevel];
		// check funding
	
		// pay
		$this->payCamp($camp
			, $config["b1"]
			, $config["b2"]
			, $config["b3"]
			, $config["p1"]
			, $config["p2"]
			);

		// create task
		$task = new Task();
		$task->objectId1 = $building->campId;
		$task->objectId2 = $buildingId;
		$task->level = $nextLevel;
		$task->finishedAt = $finishTime + $config["duration"];
		$task->type = TaskTypes::UpgradeBuilding;
		$this->repository->createTask($task);
	}

	private function payCamp($camp, $b1, $b2, $b3, $p1, $p2) {
		$camp->b1 -= $b1;
		$camp->b2 -= $b2;
		$camp->b3 -= $b3;
		$camp->p1 -= $p1;
		$camp->p2 -= $p2;
		$this->repository->payCamp($camp);
	}
	
	public function getCampQueue($campId) {
		$tasks = $this->repository->getQueue(TaskTypes::UpgradeBuilding, $campId, null);
		return $tasks;
	}
	
	/**
	 * processes all tasks that are due
	 */
	public function processTasks() {
		$this->checkGameConfig();
		if ($this->config["isTest"]) {
			$time = 9444138080;
		} else {
			$time = time();
		}
	
		$tasks = $this->repository->getDueTasks($time);
		foreach($tasks as $task) {
		var_dump($task);
			switch($task->type) {
				case TaskTypes::UpgradeBuilding:
					echo "TaskTypes::UpgradeBuilding\n";
					$this->upgradeBuilding($task);
				
				break;
				default:
				break;
			}
			$this->repository->deleteTask($task->taskId);
		}
	}
	
	private function upgradeBuilding($task) {
		$building = $this->repository->getBuildingById($task->objectId2);
		$building->level = $task->level;
		$this->repository->updateBuildingLevel($building);
		$camp = $this->repository->getCampById($task->objectId1);
		$camp->points++;
		$camp = $this->repository->updateCampPoints($camp);
		
		$camp->properties = $this->updateCampProperties($camp->properties, $building->type, $task->level);
		$camp = $this->repository->updateCampProperties($camp);
	}
	
	public function getSection($x ,$y) {
		$section = new Section();
		$section->x1 = ($x - 5) <50000 ? 50000 : ($x -5);
		$section->x2 = $x + 5;
		$section->y1 = ($y - 5) < 50000 ? 50000 : ($y-5);
		$section->y2 = $y + 5;
		$fields = $this->repository->getSection($section->x1, $section->y1, $section->x2, $section->y2, $this->contextPlayer->clanId);
		$section->fields = $fields;
		return $section;
	}
	
	public function initCamps() {
		// determine size of the field
		$numFields = $this->repository->getNumFields();
		$size = sqrt($numFields);

		$startX = 50000;
		$startY = 50000;
		$numCamps = 0;
		
		$y = $startY;
		$j = 1;
		while ($y < $startY + $size) {
			$y += 1; //rand(1, 4);
			$x = $startX;
			$previousR = 0;
			while ($x < $startX + $size) {
				do {
					$r = rand(1, 8);
				} while ($r == $previousR);
				$x += $r;
				$previousR = $r;
				$field = $this->repository->getFieldByXY($x, $y);
				if (!is_null($field)) {
					// each camp is owned by a wild fairy initially
					$player = new Player();
					$player->name = "Wild Fairy $j";
					$player->isFree = true;
					$player->points = 0;
					$player->p3 = 0;
					$player->rights = 0;
					$player = $this->repository->createPlayer($player);

					$j++;
					$camp = new Camp();
					$camp->name = "Abandoned Tree ".($numCamps+100);
					$camp->playerId = $player->playerId;
					$camp->x = $x;
					$camp->y = $y;
					$camp->b1 = 0;
					$camp->b2 = 0;
					$camp->b3 = 0;
					$camp->p1 = 0;
					$camp->p2 = 0;
					$camp->points = 0;
					
					$properties = array();
					$properties["sb1"] = 0;
					$properties["sb2"] = 0;
					$properties["sb3"] = 0;
					$properties["pb1"] = 0;
					$properties["pb2"] = 0;
					$properties["pb3"] = 0;
					$properties["def"] = 0; // defence bonus
					$properties["max"] = 0; // max total population
					$properties["maxb1"] = 0; // max people producing B1
					$properties["maxb2"] = 0; // max people producing B2
					$properties["maxb3"] = 0; // max people producing B3
					$properties["prec"] = 0; // bonus for recruiting persons, coming from keep
					$camp->properties = json_decode(json_encode($properties));
					$camp = $this->createCamp($camp);

					$field->objectId = $camp->campId;
					$field = $this->repository->updateField($field);
					
					$numCamps++;
				}
			}
		}
		return $numCamps;
	}

	private function updateCampProperties($campProperties, $type, $level) {
		switch ($type) {
			case BuildingTypes::ProducerB1:
			$campProperties->pb1 = $this->config["buildings"][$type][$level]["bonus"];
			$campProperties->maxb1 = $this->config["buildings"][$type][$level]["bonus2"];
			break;
			case BuildingTypes::ProducerB2:
			$campProperties->pb2 = $this->config["buildings"][$type][$level]["bonus"];
			$campProperties->maxb2 = $this->config["buildings"][$type][$level]["bonus2"];
			break;
			case BuildingTypes::ProducerB3:
			$campProperties->pb3 = $this->config["buildings"][$type][$level]["bonus"];
			$campProperties->maxb3 = $this->config["buildings"][$type][$level]["bonus2"];
			break;
			case BuildingTypes::StoreB1:
			$campProperties->sb1 = $this->config["buildings"][$type][$level]["bonus"];
			break;
			case BuildingTypes::StoreB2:
			$campProperties->sb2 = $this->config["buildings"][$type][$level]["bonus"];
			break;
			case BuildingTypes::StoreB3:
			$campProperties->sb3 = $this->config["buildings"][$type][$level]["bonus"];
			break;
			case BuildingTypes::Fortifications:
			$campProperties->def = $this->config["buildings"][$type][$level]["bonus"];
			break;
			case BuildingTypes::Farm:
			$campProperties->max = $this->config["buildings"][$type][$level]["bonus"];
			break;
			case BuildingTypes::Keep:
			$campProperties->prec = $this->config["buildings"][$type][$level]["bonus2"];
			break;
		}
		return $campProperties;
	}
	
	public function getPlayer($id) {
		return $this->repository->getPlayerById($id);
	}

	public function getDiplomacyOverview() {
		return $this->repository->getDiplomacyOverview();
	}

	public function getPlayerLeaderBoard() {
		return $this->repository->getLeaderboardItems();
	}
	public function getBuildings($id) {
		return $this->repository->getCampBuildings($id);
	}
	public function getBuilding($id) {
		return $this->repository->getBuildingById($id);
	}
}

?>