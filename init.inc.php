<?php

function notAuthorized() {
	//header('WWW-Authenticate: Basic realm="ZSA"');
	header('HTTP/1.0 401 Unauthorized');
	die('You are not authorized, my friend.');
}

function badAuthenticationRequest() {
	header('HTTP/1.0 400 Invalid credentials');
	die();
}

if (isset($_SERVER['PHP_AUTH_USER'])) {
	$email = $_SERVER['PHP_AUTH_USER'];
	if (isset($_SERVER['PHP_AUTH_PW'])) {
		$password = $_SERVER['PHP_AUTH_PW'];
	}
}

if (!isset($email)) {
	$email = "";
} else {
	if ($email == "") {
		badAuthenticationRequest();
	}
}

if ($cfgIsCouch) {
	$email = "343";
	$repository = new CouchRepository($cfgCouchHost);
} else {
	$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);
}

$securityManager = new SecurityManager($repository);

$contextAccount = null;
try {
	$contextAccount = $securityManager->getAccountByEmail($email);
} catch (NotFoundException $ex) {
	// username does not exist
		notAuthorized();
}
if ($contextAccount->password != $password) {
	notAuthorized();
}

?>