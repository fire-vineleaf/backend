<?php
$building = $service->getBuilding($_GET["id"]);

echo "<h1>Building: ".getBuildingName($building->type)." Level ".$building->level."</h1>";

var_dump($config["buildings"][$building->type][$building->level]);
?>

