<?php

class GrapesException extends Exception {
}
class ServiceException extends GrapesException {
}
class ModelException extends GrapesException {

	public $modelErrors = array();

	public function addModelError($propertyName, $message) {
		$this->modelErrors[$propertyName] = $message;
	}

	public function hasModelErrors() {
		return count($this->modelErrors) > 0;
	}
}
class RepositoryException extends GrapesException {
}
class ManagerException extends GrapesException {
}
class PluginException extends GrapesException {
}
class WebApiException extends GrapesException {
}
class ParameterException extends GrapesException {
}
/**
 * Ressource was not found
 * this exception will result in 404 error code in API Response
 *
 */
class NotFoundException extends GrapesException {
}
class UnauthorizedException extends GrapesException {
	
}

class ApiError {
	
	public $message;
	public $code;
	public $file;
	public $line;
	public $trace;
	public $modelErrors = null;

	/**
	 * creates an ApiError out of an exception
	 * @param Exception $exception
	 * @return ApiError
	 */
	public static function createByException($exception) {
		$apiError = new ApiError();
		$apiError->message = $exception->getMessage();
		$apiError->code = $exception->getCode();
		$apiError->file = $exception->getFile();
		$apiError->line = $exception->getLine();
		$apiError->trace = $exception->getTraceAsString();
		
		switch (get_class($exception)) {
			case "ModelException":
				$apiError->modelErrors = $exception->modelErrors;
				break;
		}
		return $apiError;
	}
}



/**
 * interface for all models
 *
 */
interface iModel {

	/**
	 * checks if this model is new:
	 * the model does not have a database id yet
	 */
	public function isNew();

	/**
	 * returns id of this model
	 */
	public function getId();

	/**
	 * returns objecttype of this model
	 * @return ObjectTypeEnum
	 */
	public function getObjectType();
	
}

/**
 * abstract base Model
 *
 */
class BaseModel {

	/**
	 * getter
	 * @param unknown $property
	 */
	public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
    }

    /**
     * setter
     * @param unknown $property
     * @param unknown $value
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
			$this->$property = $value;
        }
    }

    /**
     * converts object to json
     * @return string
     */
	public function toJson() {
		$array = (array) $this;
		return json_encode($array);
	
	}
	
	/**
	 * Creates a model and populates it with data from a repository row
	 */
	public static function createModelFromRepositoryArray($array) {	
		$rc = new ReflectionClass(get_called_class());
		$model = $rc->newInstance();
		
		foreach($model as $key => $value) {
			if (array_key_exists($key, $array)) {
				$model->$key = $array[$key];
			}
		}
		return $model;
	}
	
	/**
	 * Creates a model and populates it with data from json
	 */
	public static function createModelFromJson($json) {	
		$rc = new ReflectionClass(get_called_class());
		$model = $rc->newInstance();
		
		if ($json == "") {
			return null;
		}
				
		$jsonObject = json_decode($json);
		if ($jsonObject == null) { // json cannot be parsed
			throw new WebApiException("Request does not contain a valid JSON String");
		}
		foreach ($jsonObject AS $key => $value) {
			$model->{$key} = $value;
		}
		return $model;
	}
	
	/**
	 * retrieves objectname including name of class
	 */
	public function getObjectName() {
		return get_called_class().$this->name;		
	}
	
	public function getClassName() {
		return get_called_class();
	}
}

class ApiResponse {
	/**
	 * 
	 * @var ValidationState
	 */
	public $validationState;
	
	/**
	 * contains one single object, e.g. project
	 * @var unknown
	 */
	public $object;

	/**
	 * contains list of objects
	 * needs to be named "data" because of datatables control in web client
	 * @var array
	 */
	public $data;
}

/**
 * ActionResult
 */
class ActionResult {

	public function __construct($object, $pointsUpdate, $pointsNewTotal, $message) {
		$this->object = $object;
		$this->pointsUpdate = $pointsUpdate;
		$this->pointsNewTotal = $pointsNewTotal;	
		$this->message = $message;
	}
	
	/**
	 * the points received for this action
	 * @var int
	 */
	public $pointsUpdate = 0;

	/**
	 * new total number of points of the user that triggered this action
	 * @var int
	 */
	public $pointsNewTotal = 0;

	/**
	 * the object that this action was performed on
	 * i.e. the object that was created, updated, deleted
	 * @var unknown
	 */
	public $object;
	
	public $message;
}

/**
 * ValidationState
 *
 */
class ValidationState {

	/**
	 * 
	 * @var ValidationStateType
	 */
	public $validationStateType = ValidationStateType::Success;
	/**
	 * 
	 * @var ValidationResult
	 */
	public $validationResult = ValidationResult::OK;
	public $messages = array();

	public $points = array("update" => 0, "newTotal" => 0);

	/**
	 * adds an error to this ValidationState
	 * @param unknown $propertyName
	 * @param unknown $propertyValue
	 * @param unknown $messageText
	 */
	public function addError($propertyName, $propertyValue, $messageText) {
		$message = new ValidationMessage();
		$message->propertyName = $propertyName;
		$message->propertyValue = $propertyValue;
		$message->message = $messageText;
		$this->validationStateType = ValidationStateType::Error;
		$this->addMessage($message);
	}
	
	public function setPointsUpdate($points) {
		$this->points["update"] = $points;
	}

	public function setPointsNewTotal($points) {
		$this->points["newTotal"] = $points;
	}
	
	/**
	 * checks if this validationstate has errors
	 * @return boolean
	 */
	public function hasErrors() {
		return $this->validationStateType == ValidationStateType::Error;
	}

	/**
	 * 
	 * @param ValidationMessage $message
	 */
	public function addMessage($message) {
		//$this->messages[$message->propertyName] = $message;
		$this->messages[] = $message;
	}
	
	public static function createObjectCreated($messageText, $pointsUpdate, $pointsNewTotal) {
		$validationState = new ValidationState();
		$validationState->validationResult = ValidationResult::OKCreated;
		$validationState->validationStateType = ValidationStateType::Success;
		$validationState->points["update"] = $pointsUpdate;
		$validationState->points["newTotal"] = $pointsNewTotal;
		
		$message = new ValidationMessage();
		//$message->propertyName = $propertyName;
		//$message->propertyValue = $propertyValue;
		$message->message = $messageText;
		$validationState->addMessage($message);		
		return $validationState;
	}
}

abstract class InvitationTypes {
	const Invitation = 0;
	const Application = 1;
}

abstract class Rights {
	const _INVITE = 1;
	const _MASSMAIL = 2;
	const _MODERATOR = 4;
	const _DIPLOMACY = 8;
	const _DISMISS = 16;
	const _RIGHTS = 32;
	const _DISBAND = 64;
}

abstract class FeedItemTypes {
	/* clan specific */
	const InvitationSent = 1;
	const InvitationAccepted = 2;
	const InvitationRejected = 3;
	const ApplicationSent = 4;
	const ApplicationAccepted = 5;
	const ApplicationRejected = 6;
	const RightRevoked = 8;
	const RightGranted = 9;
	const ClanLeft = 10;
	const ClanCreated = 11;
	const DiplomacyChanged = 12;

	/* user specific */
// starting with 50
}

abstract class TaskTypes {
	const UpgradeBuilding = 1;
	const RecruitUnit = 2;
	const AttackCamp = 3;
}

abstract class DiplomacyStatus {
	const Neutral = 0;
	const Enemy = 1;
	const Ally = 2;
	const NAP = 3;
	const Vassal = 4;
	
	public static $labels = array(0 => "Neutral", 1 => "Enemy", 2 => "Ally", 3 => "NAP", 4 => "Vassal");
}

/**
 * Validation Results
 * enum
 */
abstract class ValidationResult {
	const OK = 200;
	const OKCreated = 201;
	const OKDeleted = 204;
	const NotModified = 304;
	const BadRequest = 400;
	const Unauthorized = 401;
	const Forbidden = 403;
	const NotFound = 404;
	const Unprocessable = 422;
	const Fatal = 500;
}

/**
 * Validation States
 * enum
 */
abstract class ValidationStateType {
	/**
	 * Error
	 * @var unknown
	 */
	const Error = 1;
	/**
	 * Warning
	 * @var unknown
	 */
	const Warning = 2;
	/**
	 * Success
	 * @var unknown
	 */
	const Success = 3;
	/**
	 * Info
	 * @var unknown
	 */
	const Info = 4;
}


class ValidationMessage {
	public $propertyName;
	public $propertyValue;
	public $message;
}

/**
 * ObjectType
 */
class ObjectType extends BaseModel {
	public $objectTypeId;
	public $name;
	public $description;
}

class Task extends BaseModel {
	public $taskId;
	public $objectId1;
	public $objectId2;
	public $finishedAt;
	public $type;
	public $level;
}

class Camp extends BaseModel {
	public $campId;
	public $playerId;
	public $name;
	public $x;
	public $y;
	public $b1;
	public $b2;
	public $b3;
	public $p1;
	public $p2;
	public $scores;
	public $buildings = array();
	public $player;
}

class Diplomacy extends BaseModel {
	public $diplomacyId;
	public $clan1Id;
	public $clan2Id;
	public $status;
}

class Building extends BaseModel  {
	public $buildingId;
	public $campId;
	public $type;
	public $level;
}

class Clan extends BaseModel {
	public $clanId;
	public $name;
	public $points = 0;
	public $status = 0; // diplomacystatus
}

class Section extends BaseModel {
	public $x1;
	public $y1;
	public $x2;
	public $y2;
	public $fields = array();
}

class Field extends BaseModel {
	public $fieldId;
	public $x;
	public $y;
	public $type;
	public $objectId;
	public $camp;
	public $clan;
	
	public static function createModelFromRepositoryArray($array) {
		$field = new Field();
		$field->fieldId = $array["fieldId"];
		$field->x = $array["x"];
		$field->y = $array["y"];
		$field->type = $array["type"];
		$field->objectId = $array["objectId"];
		return $field;
	}
}

class Participant extends BaseModel {
	public $messageId;
	public $playerId;
	public $isRead;
}



class Message extends BaseModel {
	public $messageId;
	public $subject;
	public $createdBy;
	public $createdAt;
	public $isRead;
	public $participants = array();
	
	public $content;
	public $createdByPlayer;
	
	
	public static function createModelFromRepositoryArray($array) {	
		$message = new Message();
		$message->messageId = $array["messageId"];
		$message->subject = $array["subject"];
		$message->createdBy = $array["createdBy"];
		$message->createdAt = $array["createdAt"];
		if(isset($array["isRead"])) {
			$message->isRead = $array["isRead"];
		}

		return $message;
	}
	
}

class Reply extends BaseModel {
	public $replyId;
	public $createdAt;
	public $createdBy;
	public $reply;
	public $messageId;
	
	public $createdByPlayer;
}

class Thread extends BaseModel  {
	public $threadId;
	public $subject;
	public $createdAt;
	public $createdBy;
	public $clanId;
	
	public $content;
}

class Post extends BaseModel  {
	public $postId;
	public $threadId;
	public $content;
	public $createdAt;
	public $createdBy;
	public $createdByPlayer;
}

class FeedItem extends BaseModel {
	public $feedItemId;
	public $playerId;
	public $clanId;
	public $createdAt;
	public $createdBy;
	public $type;
	public $payload;
}

class Invitation extends BaseModel  {
	public $invitationId;
	public $createdAt;
	public $createdBy;
	public $playerId;
	public $clanId;
	public $type;
	public $player;
	public $clan;
}
class PlayerInfo extends BaseModel {
	public $playerId;
	public $name;
	public $points;
	
	public static function createModelFromRepositoryArray($array) {	
		$info = new PlayerInfo();
		$info->playerId = $array["playerId"];
		$info->name = $array["name"];
		$info->points = $array["points"];
		return $info;
	}
}
class Account extends BaseModel {
	public $accountId;
	public $password;
	public $email;
	public $createdAt;
	public $playerId;
}

class Player extends BaseModel {
	public $playerId;
	public $name;
	public $points;
	public $clanId;
	public $rights;
	public $p3;
	public $clan;
	public $isFree;
}


class Right extends BaseModel {
	public $right;
}



?>