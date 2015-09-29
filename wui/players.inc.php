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