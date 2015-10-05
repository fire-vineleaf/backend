<div class="row">
<div class="col-md-3">
<h1>Players</h1>
<ul>
<?php
$players = $service->getPlayers();

foreach($players as $player) {
	echo "<li>";
	echoPlayer($player);
	echo "</li>";
}
?>
</ul>
</div>

<div class="col-md-3">
<h1>Leaderboard</h1>
<ul>
<?php
$items = $service->getPlayerLeaderboard();

foreach($items as $item) {
	echo "<li>";
	echo "#".$item->rank." ".$item->playerName." ".$item->playerPoints;
	echo "</li>";
}
?>
</ul>
</div>

</div>