<?php
require("../config.inc.php");
require("../includes.inc.php");
$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);

// Step 1: update player points from camp points

$repository->executeQuery("update players pl join (select c.player_id, sum(c.points) sumpoints from camps c group by c.player_id) as h on pl.player_id = h.player_id set pl.points = h.sumpoints");

// Step 2: update player leaderboard
$repository->resetLeaderboard();

$rank = 0;
$curPoints = -1;
$players = $repository->getPlayers();
foreach($players as $player) {
	$item = new leaderboardItem();
	$item->playerId = $player->playerId;
	$item->playerName = $player->name;
	$item->playerPoints= $player->points;
	
	if ($player->points <> $curPoints) {
		$rank++;
		$curPoints = $player->points;
	}
	$item->rank = $rank;
	$item = $repository->createLeaderboardItem($item);
	
}

?>
done