<?php
require("routes.inc.php");
require("includes.inc.php");
$webApi = array();

$webApi["version"] = "1.0";
$paths = array();
foreach($routes as $route) {

	$pathName = $route[1];
	if (isset($paths[$pathName])) {
		$path = $paths[$pathName];
	} else {
		$path = array();
		$path["methods"] = array();
		$path["path"] = $route[1];
		$path["summary"] = "";
	}
	$method["name"] = strtolower($route[0]);
	$method["meta"] = $route[2];
	
	$path["methods"][] = $method;
	$paths[$pathName] = $path;
}
$webApi["paths"] = $paths;

$webApi["models"] = array();

$camp = new Camp();
$camp->campId = 1;
$camp->userId = 12;
$camp->name = "camp name";
$webApi["models"]["camp"] = $camp;

$clan = new Clan();
$clan->clanId = 1;
$clan->name = "clan name";
$webApi["models"]["clan"] = $clan;

$right = new Right();
$right->right = 64;
$webApi["models"]["right"] = $right;

$thread = new Thread();
$thread->subject = "subject";
$thread->content = "content, body, ...";
$webApi["models"]["thread"] = $thread;

$post = new Post();
$post->content = "this is my reply!";
$webApi["models"]["post"] = $post;

$message = new Message();
$message->subject = "Hey, what' up?";
$message->participants[] = 12;
$message->participants[] = 5654;
$message->participants[] = 98203;
$webApi["models"]["message"] = $message;

$reply = new Reply();
$reply->reply = "All fine, my friend";
$webApi["models"]["reply"] = $message;


// done
echo json_encode($webApi);
?>