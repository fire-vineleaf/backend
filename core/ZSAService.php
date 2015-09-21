<?php

class CampBuilding {
	public $name;
	public $level;
	public $type;
	public $numPersons;
}

class ZSAService extends BaseService {

	public function getCamp($id) {
		$camp = $this->repository->getCampById($id);
		$camp->buildings = $this->repository->getCampBuildings($camp->campId);
		return $camp;
	}

	public function getCamps($userId) {
	if (is_null($userId)) {
		return $this->getOwnCamps();
	} else {
		return $this->getUserCamps($userId);
	}
	}

	public function getOwnCamps() {
		return $this->getUserCamps($this->contextUser->userId);
	}

	
	private function getUserCamps($userId) {
		$camps = $this->repository->getUserCamps($userId);
		return $camps;
	}

	public function createCamp($camp) {
		$camp = $this->repository->createCamp($camp);
		
		for ($i=0;$i<13;$i++) {
			$building = new Building();
			$building->type = $i;
			$building->campId = $camp->campId;
			$building->level = 1;
			$building = $this->repository->createBuilding($building);
		}
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
		$user = new User();
		$user = $this->contextUser;
		$user->clanId = $clan->clanId;
		$user->rights = 
			Rights::_INVITE
			| Rights::_MASSMAIL
			| Rights::_MODERATOR
			| Rights::_DIPLOMACY
			| Rights::_DISMISS
			| Rights::_RIGHTS
			| Rights::_DISBAND;
		$user = $this->repository->updateUserClan($user);
		$user = $this->repository->updateUserRights($user);
			
		$item = $this->createFeedItem(FeedItemTypes::CreatedClan);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);

		return $clan;
	}
	
	public function leaveClan() {
		$this->removeUserFromClan($this->contextUser);
		$item = $this->createFeedItem(FeedItemTypes::InvitationAccepted);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);

		$item = $this->createFeedItem(FeedItemTypes::LeftClan);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}
	
	public function disbandClan() {
		$clanId = $this->contextUser->clanId;
		$members = $this->repository->getClanMembers($clanId);
		foreach($members as $member) {
			$this->removeUserFromClan($member);
		}
		$this->repository->deleteClan($clanId);
	}
	
	private function removeUserFromClan($user) {
		$user->clanId = null;
		$user->rights = 0;
		$this->repository->updateUserClan($user);
		$this->repository->updateUserRights($user);
	}
	
	public function inviteUser($userId) {
		$invitation = new Invitation();
		$invitation->createdAt = time();
		$invitation->createdBy = $this->contextUser->userId;
		$invitation->clanId = $this->contextUser->clanId;
		$invitation->userId = $userId;
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
		$invitation->createdBy = $this->contextUser->userId;
		$invitation->clanId = $clanId;
		$invitation->userId = $this->contextUser->userId;
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
		$user = $this->repository->getUserById($invitation->userId);
		$user->clanId = $invitation->clanId;
		$user->rights = 0;
		$user = $this->repository->updateUserClan($user);
		$user = $this->repository->updateUserRights($user);

		$this->repository->deleteInvitation($invitationId);
		$item = $this->createFeedItem(FeedItemTypes::InvitationAccepted);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}

	public function rejectInvitation($invitationId) {
		$this->repository->deleteInvitation($invitationId);
		$item = $this->createFeedItem(FeedItemTypes::InvitationRejected);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}

	public function grantRight($userId, $right) {
		$user = $this->repository->getUserById($userId);
		$this->checkIsFound($user);
		$user->rights |= $right->right;
		$user = $this->repository->updateUserRights($user);
		$item = $this->createFeedItem(FeedItemTypes::RightGranted);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}
	
	public function revokeRight($userId, $right) {
		$user = $this->repository->getUserById($userId);
		$this->checkIsFound($user);
		$user->rights &= ~$right->right;
		$user = $this->repository->updateUserRights($user);
		$item = $this->createFeedItem(FeedItemTypes::RightRevoked);
		$item->payload = "todo: payload"; // todo: payload
		$this->repository->createFeedItem($item);
	}
	
	
	
	private function createFeedItem($type) {
		$item = new FeedItem();
		$item->createdAt = time();
		$item->createdBy = $this->contextUser->userId;
		if ($type >=50) {
			$item->userId = $this->contextUser->userId;
		} else {
			$item->clanId = $this->contextUser->clanId;
		}
		$item->type = $type;
		$item->payload = "";
		return $item;
	}
	
	
	/**
	 * updates clan of a user
	 * @param User $user
	 */
	public function updateUserClan($user) {
		$this->repository->updateUserClan($user);
	}

	/**
	 * Creates a new Thread
	 * @param Thread $thread
	 * @return Thread
	 */
	public function createThread($thread) {
		$thread->createdBy = $this->contextUser->userId;
		$thread->createdAt = time();
		$thread->clanId = $this->contextUser->clanId;
		$thread = $this->repository->createThread($thread);
		$post = new Post();
		$post->threadId = $thread->threadId;
		$post->createdBy = $this->contextUser->userId;
		$post->createdAt = time();
		$post->content = $thread->content;
		$post = $this->repository->createPost($post);
		return $thread;
	}

	public function createPost($threadId, $post) {
		$post->threadId = $threadId;
		$post->createdBy = $this->contextUser->userId;
		$post->createdAt = time();
		$post = $this->repository->createPost($post);
		return $post;
	}

	public function createMessage($message) {
		$message->createdAt = time();
		$message->createdBy = $this->contextUser->userId;
		$message = $this->repository->createMessage($message);
		// and don't forget to add yourself as participant!
		$message->participants[] = $this->contextUser->userId;
		foreach($message->participants as $userId) {
			$participant = new Participant();
			$participant->userId = $userId;
			$participant->messageId = $message->messageId;
			$this->repository->createParticipant($participant);
		}
		$reply = new Reply();
		$reply->createdAt = time();
		$reply->createdBy = $this->contextUser->userId;
		$reply->reply = $message->content;
		$reply->messageId = $message->messageId;
		$reply = $this->repository->createReply($reply);
		return $message;
	}
	
	public function getClanMembers($id) {
		$members = $this->repository->getClanMembers($id);
		return $members;
	}

	public function replyToMessage($id, $reply) {
		$reply->createdAt = time();
		$reply->createdBy = $this->contextUser->userId;
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
		$this->repository->deleteParticipant($this->contextUser->userId, $messageId);
		// check if this was the last participant
		// if yes: delete message and all replies
		$numParticipants = $this->repository->getNumParticipants($messageId);
		if ($numParticipants == 0) {
			$this->repository->deleteMessage($messageId);
			$this->repository->deleteMessageReplies($messageId);
		}
	}
	
	public function getMessages() {
		$messages = $this->repository->getMessages($this->contextUser->userId);
		return $messages;
	}
	
	public function getMessage($messageId) {
		$message = $this->repository->getMessageById($messageId);
		// mark message as read for this user
		$participant = new Participant();
		$participant->userId = $this->contextUser->userId;
		$participant->messageId = $messageId;
		$participant->isRead = 1;
		$this->repository->updateParticipant($participant);
		return $message;
	}

	public function getReplies($messageId) {
		$replies = $this->repository->getReplies($messageId);
		return $replies;
	}

	public function queueUpgradeBuilding($buildingId) {
		$building = $this->repository->getBuildingById($buildingId);
		$camp = $this->repository->getCampById($building->campId);
		// determine costs and duration
		$previousTask = $this->repository->getPreviousBuildingTask($buildingId);
		$lastTask = $this->repository->getLastCampTask($camp->campId);
		if (is_null($previousTask)) {
			$nextLevel = 1;
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
		var_dump($building);
		var_dump($nextLevel);
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
		$tasks = $this->repository->getDueTasks();
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
		$building = new Building();
		$building->buildingId = $task->objectId2;
		$building->level = $task->level;
		
		$this->repository->updateBuildingLevel($building);
	}
	
	public function getSection($x ,$y) {
		$section = new Section();
		$section->x1 = $x - 5;
		$section->x2 = $x + 5;
		$section->y1 = $y - 10;
		$section->y2 = $y + 10;
		$fields = $this->repository->getSection($section->x1, $section->y1, $section->x2, $section->y2);
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
		while ($y < $startY + $size) {
			$y += 1; //rand(1, 4);
			$x = $startX;
			$previousR = 0;
			while ($x < $startX + $size) {
				do {
					$r = rand(1, 4);
				} while ($r == $previousR);
				$x += $r;
				$previousR = $r;
				$field = $this->repository->getFieldByXY($x, $y);
				if (!is_null($field)) {
					$camp = new Camp();
					$camp->name = "Abandoned Tree ".($numCamps+100);
					$camp->userId = 1;
					$camp->x = $x;
					$camp->y = $y;
					$camp = $this->createCamp($camp);

					$field->objectId = $camp->campId;
					$field = $this->repository->updateField($field);
					
					$numCamps++;
				}
			}
		}
		return $numCamps;
	}
	
	public function getUser($id) {
		return $this->repository->getUserById($id);
	}


}

?>