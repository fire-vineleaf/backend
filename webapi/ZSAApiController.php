<?php
/**
 * Base class WebApi
 */
class BaseWebApiController {
	public $contextUser;
	public $mySqlRepository;
	public $config;	
}
class ZSAApiController extends BaseWebApiController {

	private function getZSAService() {
		$service = new ZSAService($this->contextUser, $this->repository);
		$service->config = $this->config;
		return $service;
	}

	public function getCamps($parameters) {
		if (isset($parameters["id"])) {
			$id = $parameters["id"];
		} else {
			$id = null;
		}
		$service = $this->getZSAService();
		$camps = $service->getCamps($id);
		return $camps;
	}

	public function getOwnCamps() {
		$service = $this->getZSAService();
		$camps = $service->getOwnCamps();
		return $camps;
	}
	
	
	public function getCamp($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$camp = $service->getCamp($id);
		return $camp;
	}
	
	public function createClan() {
		$body = file_get_contents('php://input');

		$clan = Clan::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);
		$clan = $service->createClan($clan);
		return $clan;
	}

	public function getClanMembers($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$members = $service->getclanMembers($id);
		return $members;
	}

	public function leaveClan() {
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->leaveClan();
	}
	
	public function disbandClan() {
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->disbandClan();
	}

	public function inviteUser($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$invitation = $service->inviteUser($id);
		return $invitation;
	}
	
	public function applyForClan($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$invitation = $service->applyForClan($id);
		return $invitation;
	}
	
	public function acceptInvitation($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->acceptInvitation($id);
	}
	
	public function rejectInvitation($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->rejectInvitation($id);
	}
	
	public function grantRight($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$right = Right::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->grantRight($id, $right);
	}

	public function revokeRight($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$right = Right::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->revokeRight($id, $right);
	}
	
	public function createThread() {
		$body = file_get_contents('php://input');
		$thread = Thread::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);		
		$thread = $service->createThread($thread);
		return $thread;
	}
	
	public function createPost($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$post = Post::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);		
		$post = $service->createPost($id, $post);
		return $post;
	}

	public function createMessage() {
		$body = file_get_contents('php://input');
		$message = Message::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);		
		$message = $service->createMessage($message);
		return $message;
	}

	public function replyToMessage($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$reply = Reply::createModelFromJson($body);
		$service = new ZSAService($this->contextUser, $this->repository);		
		$reply = $service->replyToMessage($id, $reply);
		$reply->reply = ""; // delete reply to keep response small, reply is not needed in this case
		return $reply;
	}

	public function deleteMessage($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$service->deleteMessage($id);
	}
	
	public function getMessages() {
		$service = new ZSAService($this->contextUser, $this->repository);		
		$messages = $service->getMessages();
		return $messages;
	}
	
	public function getMessage($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$message = $service->getMessage($id);
		return $message;
	}
	
	public function getReplies($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextUser, $this->repository);		
		$replies = $service->getReplies($id);
		return $replies;
	}

	public function upgradeBuilding($parameters) {
		$id = $parameters["id"];
		$service = $this->getZSAService();
		$service->queueUpgradeBuilding($id);
	}

	public function getCampQueue($parameters) {
		$id = $parameters["id"];
		$service = $this->getZSAService();
		$tasks = $service->getCampQueue($id);
		return $tasks;
	}
	
	public function getSection($parameters) {
		$x = $parameters["x"];
		$y = $parameters["y"];
		$service = $this->getZSAService();
		$section = $service->getSection($x ,$y);
		return $section;
	}

	public function getUser($parameters) {
		$id = $parameters["id"];
		$service = $this->getZSAService();
		$user = $service->getUser($id);
		return $user;
	}
}


?>