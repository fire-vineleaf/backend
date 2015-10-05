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
echo "<p>b1: ".$camp->b1."&nbsp;|&nbsp;";
echo "b2: ".$camp->b2."&nbsp;|&nbsp;";
echo "b3: ".$camp->b3."&nbsp;|&nbsp;";
echo "p1: ".$camp->p1."&nbsp;|&nbsp;";
echo "p2: ".$camp->p2."&nbsp;|&nbsp;";
echo "points: ".$camp->points."</p>";

?>

<table border=1 width=100%>
<tr>
	<th>buildings</th>
	<th>queue</th>
</tr>
<tr>
	<td>
		<ul>
	<?php
	foreach ($camp->buildings as $b) {
		echo "<li>";
		echo "id: ".$b->buildingId.", type: ".$b->type.", level: ".$b->level;
		if (isset($config["buildings"][$b->type][$b->level+1])) {
			echo " - <a href=\"?page=managecamp&id=$id&buildingId=".$b->buildingId."\">Upgrade</a>";
		}
		echo "</li>";
	}
	?>
		</ul>
	</td>
	<td>
	<ul>
	<?php
	foreach ($tasks as $t) {
		echo "<li>";
		echo "id: ".$t->taskId.", finishedAt: ".date(DATE_RFC822, $t->finishedAt).", type: ".$t->type.", oid1: ".$t->objectId1.", oid2: ".$t->objectId2.", level: ".$t->level;
		echo "</li>";
	}
	?>
	</ul>
	</td>
</tr>
</table>

