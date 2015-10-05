<?php
require("../config.inc.php");
require("../includes.inc.php");
$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);

$player = new Player();
$player->playerId = 1;

$service = new ZSAService($player, $repository);
$service->config = $config;
$service->processTasks();

?>
done