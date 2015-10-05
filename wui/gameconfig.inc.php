<?php
function duration($dur) {
	if ($dur >= 3600) {
		return sprintf('%02d', floor($dur/(60*60))).":".hours($dur %(3600));
	}
	return hours($dur);
}

function hours($dur) {
	return sprintf('%02d',floor($dur/60)).":".sprintf('%02d',($dur%60));
}


?>
<style>
td {
	text-align: right;
}
th {
	text-align: right;
}
</style>
<table border=1>
<tr>
	<th>Building</th>
	<th>Level</th>
	<th>Duration</th>
	<th>Costs 1</th>
	<th>Costs 2</th>
	<th>Costs 3</th>
</tr>
<?php
$i = 0;
$totalDuration = 0;
$totalB1 = 0;
$totalB2 = 0;
$totalB3 = 0;
$subTotalDuration = 0;
$subTotalB1 = 0;
$subTotalB2 = 0;
$subTotalB3 = 0;

foreach($config["buildings"] as $typeId => $type) {
	$j = 2;
	foreach($type as $levelId => $level) {
		echo "<tr>";
		echo "<td>".$typeId."</td>";
		echo "<td>".$levelId."</td>";
		echo "<td>".duration($level["duration"])."</td>";
		echo "<td>".$level["b1"]."</td>";
		echo "<td>".$level["b2"]."</td>";
		echo "<td>".$level["b3"]."</td>";
		echo "</tr>";
		$j++;
		$totalDuration += $level["duration"];
		$totalB1 += $level["b1"];
		$totalB2 += $level["b2"];
		$totalB3 += $level["b3"];
		$subTotalDuration += $level["duration"];
		$subTotalB1 += $level["b1"];
		$subTotalB2 += $level["b2"];
		$subTotalB3 += $level["b3"];
	}
	echo "<tr>";
	echo "<th></th>";
	echo "<th></th>";
	echo "<th>".duration($subTotalDuration)."</th>";
	echo "<th>$subTotalB1</th>";
	echo "<th>$subTotalB2</th>";
	echo "<th>$subTotalB3</th>";
	echo "</tr>";
	
	$subTotalDuration = 0;
	$subTotalB1 = 0;
	$subTotalB2 = 0;
	$subTotalB3 = 0;

	$i++;
}
echo "<tr>";
echo "<th></th>";
echo "<th></th>";
echo "<th>".duration($totalDuration)."</th>";
echo "<th>$totalB1</th>";
echo "<th>$totalB2</th>";
echo "<th>$totalB3</th>";
echo "</tr>";
?>
</table>