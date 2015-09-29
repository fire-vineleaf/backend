<h1>Clans</h1>

<ul>
<?php
$clans = $service->getClans();

foreach($clans as $clan) {
	echo "<li>";
	echoClan($clan);
	echo "</li>";
}
?>
</ul>