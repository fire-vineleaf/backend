<?php
/**
 * Base class WebApi
 */
class BaseWebApiController {
	public $contextPlayer;
	public $contextAccount;
	public $mySqlRepository;
	public $config;	
}
class ZSAApiController extends BaseWebApiController {

	private function getZSAService() {
		$service = new ZSAService($this->contextPlayer, $this->repository);
		$service->config = $this->config;
		$service->contextAccount = $this->contextAccount;
		return $service;
	}

	public function getCamps($parameters) {
		$service = $this->getZSAService();
		if (isset($parameters["id"])) {
			$id = $parameters["id"];
		} else {
			$id=null;
		}
		$camps = $service->getCamps($id);
		return $camps;
	}

	
	public function getCamp($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$camp = $service->getCamp($id);
		return $camp;
	}
	
	public function createClan() {
		$body = file_get_contents('php://input');

		$clan = Clan::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);
		$clan = $service->createClan($clan);
		return $clan;
	}

	public function getClanMembers($parameters) {
		$id = $this->getId($parameters);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$members = $service->getclanMembers($id);
		return $members;
	}

	public function getThreads() {
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$threads = $service->getThreads();
		return $threads;
	}

	public function getLeaderboardPlayers() {
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$players = $service->getLeaderBoardPlayers();
		return $players;
	}

	public function getPosts($parameters) {
		$id = $this->getId($parameters);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$posts = $service->getPosts($id);
		return $posts;
	}
	
	public function leaveClan() {
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->leaveClan();
	}
	
	public function disbandClan() {
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->disbandClan();
	}

	public function inviteUser($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$invitation = $service->inviteUser($id);
		return $invitation;
	}
	
	public function applyForClan($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$invitation = $service->applyForClan($id);
		return $invitation;
	}
	
	public function acceptInvitation($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->acceptInvitation($id);
	}
	
	public function rejectInvitation($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->rejectInvitation($id);
	}
	
	public function grantRight($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$right = Right::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->grantRight($id, $right);
	}

	public function revokeRight($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$right = Right::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->revokeRight($id, $right);
	}
	
	public function createThread() {
		$body = file_get_contents('php://input');
		$thread = Thread::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$thread = $service->createThread($thread);
		return $thread;
	}
	
	public function createPost($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$post = Post::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$post = $service->createPost($id, $post);
		return $post;
	}

	public function createMessage() {
		$body = file_get_contents('php://input');
		$message = Message::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$message = $service->createMessage($message);
		return $message;
	}

	public function replyToMessage($parameters) {
		$id = $parameters["id"];
		$body = file_get_contents('php://input');
		$reply = Reply::createModelFromJson($body);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$reply = $service->replyToMessage($id, $reply);
		$reply->reply = ""; // delete reply to keep response small, reply is not needed in this case
		return $reply;
	}

	public function deleteMessage($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$service->deleteMessage($id);
	}
	
	public function getMessages() {
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$messages = $service->getMessages();
		return $messages;
	}
	
	public function getInvitations() {
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$invitations = $service->getOwnInvitations();
		return $invitations;
	}
	
	public function getAccount() {
		$this->contextAccount->password = null; // security
		return $this->contextAccount;
	}

	public function getPlayer($parameters) {
		$id = $this->getId($parameters);
		if (is_null($id)) {
			return $this->contextPlayer;
		} else {
			$service = new ZSAService($this->contextPlayer, $this->repository);		
			$player = $service->getPlayer($id);
			return $player;
		}
	}
	
	public function getMessage($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$message = $service->getMessage($id);
		return $message;
	}

	private function getId($parameters) {
		if (isset($parameters["id"])) {
			return $parameters["id"];
		} else {
			return null;
		}
	}
	public function getClan($parameters) {
		$id = $this->getId($parameters);
		$service = new ZSAService($this->contextPlayer, $this->repository);		
		$clan = $service->getClan($id);
		return $clan;
	}
	
	public function getReplies($parameters) {
		$id = $parameters["id"];
		$service = new ZSAService($this->contextPlayer, $this->repository);		
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

}


?>