<h1>Diplomacy</h1>

<table border=1>
<tr>
	<th>Clan 1</th>
	<th>Clan 2</th>
	<th>Status</th>
</tr>
<?php
$ds = $service->getDiplomacyOverview();
foreach($ds as $d) {
	echo "<tr>";
	echo "<td>";
	echoClan($d["clan1"]);
	echo "</td>";
	echo "<td>";
	echoClan($d["clan2"]);
	echo "</td>";
	echo "<td>";
	echo DiplomacyStatus::$labels[$d["status"]];
	echo "</td>";
	echo "</tr>";
}
?>
</table>