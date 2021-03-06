<?php
$id = $_GET["id"];

if (isset($_GET["buildingId"])) {
	$buildingId = $_GET["buildingId"];
	try {
		$service->queueUpgradeBuilding($buildingId);
	} catch (Exception $ex) {
		echoException($ex);
	}
}

$camp = $service->getCamp($id);
$buildings = $service->getBuildings($id);
$tasks = $service->getCampQueue($id);

?>

<div class="row">
	<div class="col-md-5">
	<?php
	echo "<div class=\"panel panel-default\">";
	echo "<div class=\"panel-heading\">";
	echo "<a href=\"?page=managecamp&id=".$camp->campId."\">".$camp->name."</a> (id: ".$camp->campId.")";
	echo "b1: ".$camp->b1."/".$camp->properties->sb1."&nbsp;|&nbsp;";
	echo "b2: ".$camp->b2."/".$camp->properties->sb2."&nbsp;|&nbsp;";
	echo "b3: ".$camp->b3."/".$camp->properties->sb3."&nbsp;|&nbsp;";
	echo "p1: ".$camp->p1."&nbsp;|&nbsp;";
	echo "p2: ".$camp->p2."&nbsp;|&nbsp;";
	echo "people: ".$camp->people."&nbsp;|&nbsp;";
	echo "points: ".$camp->points."";
	echo "</div>";
	//echo "<div class=\"panel-body\">";
	//echo "</div>";
	echo "<ul class=\"list-group\">";
	foreach ($camp->buildings as $b) {

		echo "<li class=\"list-group-item\">";	echoBuilding($b);

		if (isset($config["buildings"][$b->type][$b->level])) {
			$c = $config["buildings"][$b->type][$b->level];
			echo "&nbsp;";
			switch ($b->type) {
				case BuildingTypes::ProducerB1:
				echo $c["bonus"]." B1/h";
				echo "&nbsp;&nbsp;People: ".$b->people;
				break;
				case BuildingTypes::ProducerB2:
				echo $c["bonus"]." B2/h";
				echo "&nbsp;&nbsp;People: ".$b->people;
				break;
				case BuildingTypes::ProducerB3:
				echo $c["bonus"]." B3/h";
				echo "&nbsp;&nbsp;People: ".$b->people;
				break;
				case BuildingTypes::StoreB1:
				echo $c["bonus"]." B1";
				break;
				case BuildingTypes::StoreB2:
				echo $c["bonus"]." B2";
				break;
				case BuildingTypes::StoreB3:
				echo $c["bonus"]." B3";
				break;
				case BuildingTypes::Fortifications:
				echo "+".$c["bonus"]."%";
				break;
				default:
				echo "Effekt: ". $c["bonus"];
				break;
			}
			
		}

		if (isset($config["buildings"][$b->type][$b->level+1])) {
			$c = $config["buildings"][$b->type][$b->level+1];
			echo "<br/><a href=\"?page=managecamp&id=$id&buildingId=".$b->buildingId."\">Upgrade</a> - B1: ".$c["b1"]." B2: ".$c["b2"]." B3: ".$c["b3"];
		}
		echo "</li>";
	}
	echo "</ul>";
	echo "</div>";
	?>
	</div>
	<div class="col-md-3">
	<?php var_dump($camp->properties);?>
	</div>
	<div class="col-md-4">
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
