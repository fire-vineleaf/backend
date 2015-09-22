<?php

$cfgIsCouch = false;
$cfgCouchHost = "127.0.0.1:8091";

$cfgMySqlHost =  getenv('OPENSHIFT_MYSQL_DB_HOST');
$cfgMySqlUserName = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
$cfgMySqlPassword = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');
$cfgMySqlPort = getenv('OPENSHIFT_MYSQL_DB_PORT');
$cfgMySqlDatabase= "zsa";

if ($cfgMySqlHost == "") {
	$cfgMySqlHost =  "p:localhost";
	$cfgMySqlUserName = "root";
	$cfgMySqlPassword = "";
	$cfgMySqlPort ="";
}


?>