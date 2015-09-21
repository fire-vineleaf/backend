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


require ("config.inc.php.conf");
require("includes.inc.php");
require("init.inc.php");

$action = $_GET["a"];
$controller = new ZSAApiController();
$controller->repository = $repository;
$controller->config = $config;
$controller->contextUser = $contextUser;

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
		case "user":
		$method = "getUser";
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
		default:
		header("HTTP/1.0 400 Bad Request");
		$requestUri = $_SERVER["REQUEST_URI"];
		echo "{\"error\": \"No matching route found for '".$requestUri."'\"}";
		die();
		break;
	}
	
	
	break;
	case "post":
	break;
	case "put":
	break;
	case "delete":
	break;
	default:
	die("invalid request method: $requestMethod");
	break;
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
