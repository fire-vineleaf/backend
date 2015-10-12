<?php
require_once("../config.inc.php");
require_once("../gameconfig.inc.php");
require_once("../includes.inc.php");
error_reporting (E_ALL);
$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);

$player = new Player();
$player->playerId = 1;

$service = new ZSAService($player, $repository);
$service->config = $config;
$service->processTasks();

?>