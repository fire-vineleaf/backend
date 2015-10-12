<?php
$id = $_GET["id"];

if (isset($_GET["buildingId"])) {
	$buildingId = $_GET["buildingId"];
	$service->queueUpgradeBuilding($buildingId);
	
}

$camp = $service->getCamp($id);
$buildings = $service->getBuildings($id);
$tasks = $service->getCampQueue($id);

echo "<h2>".$camp->name." (".$camp->campId.")</h2>";
echo "<p>b1: ".$camp->b1."/".$camp->properties->sB1."&nbsp;|&nbsp;";
echo "b2: ".$camp->b2."/".$camp->properties->sB2."&nbsp;|&nbsp;";
echo "b3: ".$camp->b3."/".$camp->properties->sB3."&nbsp;|&nbsp;";
echo "p1: ".$camp->p1."&nbsp;|&nbsp;";
echo "p2: ".$camp->p2."&nbsp;|&nbsp;";
echo "points: ".$camp->points."</p>";

?>

<div class="row">
	<div class="col-md-5">
	<?php
	foreach ($camp->buildings as $b) {
		echo "<div class=\"panel panel-default\">";
		echo "<div class=\"panel-heading\">";
		echoBuilding($b);
		echo "</div>";
		echo "<div class=\"panel-body\">";

		if (isset($config["buildings"][$b->type][$b->level])) {
			$c = $config["buildings"][$b->type][$b->level];
			echo $c["bonus"];
		}

		if (isset($config["buildings"][$b->type][$b->level+1])) {
			$c = $config["buildings"][$b->type][$b->level+1];
			echo "<br/><a href=\"?page=managecamp&id=$id&buildingId=".$b->buildingId."\">Upgrade</a> - B1: ".$c["b1"]." B2: ".$c["b2"]." B3: ".$c["b3"];
		}

		echo "</div>";
		echo "</div>";
	}
	?>
	</div>
	<div class="col-md-5">
	
	<ul>
	<?php
	foreach ($tasks as $t) {
		echo "<li>";
		echo "id: ".$t->taskId.", finishedAt: ".date(DATE_RFC822, $t->finishedAt).", type: ".$t->type.", oid1: ".$t->objectId1.", oid2: ".$t->objectId2.", level: ".$t->level;
		echo "</li>";
	}
	?>
	</ul>
	</div>
</div>
