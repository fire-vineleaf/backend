<?php
error_reporting ( E_ALL );


register_shutdown_function("fatal_handler");
function fatal_handler() {
	$error = error_get_last();
	if (is_array($error)) {	
		$errorMessage = $error["message"]. " in ". $error["file"]. " in line " . $error["line"];
		header("HTTP/1.0 500 ZSA in fatal trouble!");
		
		$actionResult = new ActionResult(null, 0,0, $errorMessage);
		echo json_encode($actionResult);
	}	
}

require ("config.inc.php");
require("includes.inc.php");
require("init.inc.php");

$action = $_GET["a"];
$controller = new ZSAApiController();
$controller->repository = $repository;
$controller->config = $config;
$player = $repository->getPlayerById($contextAccount->playerId);
if (is_null($player)) {
	$e["error"] = "player does not exist";
	die(json_encode($e));
}
$controller->contextPlayer = $player;

	
$parameters = array();
if (isset($_SERVER["QUERY_STRING"])) {
	$ps = explode("&", $_SERVER["QUERY_STRING"]);
	foreach($ps as $p) {
		$vs = explode("=", $p);
		$parameters[$vs[0]] = $vs[1];
	}
}
$requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);
switch ($requestMethod) {
	case "get":
	switch ($action) {
		case "player":
		$method = "getPlayer";
		break;
		case "camps":
		$method = "getCamps";
		break;
		case "camp":
		$method = "getCamp";
		break;
		case "messages":
		$method = "getMessages";
		break;
		case "campqueue":
		$method = "getCampQueue";
		break;
		case "section":
		$method = "getSection";
		break;
		case "members":
		$method = "getClanMembers";
		break;
		case "message":
		$method = "getMessage";
		break;
		case "replies":
		$method = "getReplies";
		break;
		default:
		invalidRoute();
		break;
	}
	
	
	break;
	case "post":
	switch ($action) {
		case "clan":
		$method = "createClan";
		break;
		case "leave":
		$method = "leaveClan";
		break;
		case "invite":
		$method = "invitePlayer";
		break;
		case "apply":
		$method = "applyForClan";
		break;
		case "accept":
		$method = "acceptInvitation";
		break;
		case "reject":
		$method = "rejectInvitation";
		break;
		case "right":
		$method = "grantRight";
		break;
		case "thread":
		$method = "createThread";
		break;
		case "post":
		$method = "createPost";
		break;
		case "message":
		$method = "createMessage";
		break;
		case "reply":
		$method = "replyToMessage";
		break;
		case "building":
		$method = "upgradeBuilding";
		break;
		default:
		invalidRoute();
		break;
	}
	break;
	case "put":
	break;
	case "delete":
	switch ($action) {
		case "disband":
		$method = "disbandClan";
		break;
		case "right":
		$method = "revokeRight";
		break;
		default:
		invalidRoute();
		break;
	}
	break;
	default:
	die("invalid request method: $requestMethod");
	break;
}

function invalidRoute() {
	global $requestMethod;
		header("HTTP/1.0 400 Bad Request");
		$requestUri = $_SERVER["REQUEST_URI"];
		echo "{\"error\": \"No matching route found for ".strtoupper($requestMethod)." '".$requestUri."'\"}";
		die();
}



// execute controller
$httpCode = "HTTP/1.0 ";

try {


	$result = $controller->$method($parameters);
	
	switch($_SERVER['REQUEST_METHOD']) {
		case "POST":
			$httpCode = "HTTP/1.0 201 A new object was born";
			break;
		case "PUT":
			$httpCode = "HTTP/1.0 200 updated";
			break;
		case "DELETE":
			// returning 200 instead of 204 because an entity is returned
			$httpCode = "HTTP/1.0 200 It's gone now...";
			break;
		default: // GET
			$httpCode = "HTTP/1.0 200 There you go";
			break;
	}
}
catch (ServiceException $ex)
{
	$httpCode = "HTTP/1.0 500 ServiceException";
	$result = ApiError::createByException($ex);
}
catch (ModelException $ex)
{
	$httpCode = "HTTP/1.0 422 Can't process that object";	
	$result = ApiError::createByException($ex);
}
catch (RepositoryException $ex)
{
	$httpCode = "HTTP/1.0 500 Repository Exception";	
	$result = ApiError::createByException($ex);
}
catch (ManagerException $ex)
{
	$httpCode = "HTTP/1.0 500 Manager Exception";	
	$result = ApiError::createByException($ex);
}
catch (PluginException $ex)
{
	$httpCode = "HTTP/1.0 500 Plugin is causing trouble";	
	$result = ApiError::createByException($ex);
}
catch (WebApiException $ex)
{
	$httpCode = "HTTP/1.0 500 WebApi Exception";	
	$result = ApiError::createByException($ex);
}
catch (ParameterException $ex)
{
	$httpCode = "HTTP/1.0 400 Something's bad about your request";	
	$result = ApiError::createByException($ex);
}
catch (NotFoundException $ex)
{
	$httpCode = "HTTP/1.0 404 Not Found";	
	$result = ApiError::createByException($ex);
}
catch (UnauthorizedException $ex)
{
	$httpCode = "HTTP/1.0 403 This is above your pay grade";	
	$result = ApiError::createByException($ex);
}
catch (Exception $ex)
{
	$httpCode = "HTTP/1.0 500 ZSA in trouble!";	
	$result = ApiError::createByException($ex);
}

//ob_start("ob_gzhandler");
header($httpCode);
header("Content-Type: application/json");
if (is_string($result)) {
	echo $result;
} else {
	if (get_parent_class($result) == "BaseModel") {
		echo $result->toJson();
	} else {
		echo json_encode($result);
	}
}
//ob_end_flush();
?>
