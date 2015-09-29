<?php
if (isset($_GET["id"])) {
	$id = $_GET["id"];
	$player = $service->getPlayer($id);
} else {
	$player = $contextPlayer;
}
$hasClan = !is_null($player->clanId);
?>
<h1>Player <?php echo $player->name; ?></h1>
<p>Clan: <?php echo $player->clan->name; ?>
<p>Points: <?php echo $player->points; ?>
<p>rights: <?php echo $player->rights; ?></p>
<?php echoPlayerRights($player, false); ?>
<h2>Camps</h2>
<ul>
<?php
$camps = $service->getCamps($player->playerId);
foreach($camps as $camp) {
	echo "<li>";
	echoCamp($camp);
	echo "</li>";
}
?>
</ul>