<?php
$id = $_GET["id"];

$url = $baseUrl."?a=camp&id=$id";
var_dump($url);
$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($email, $password)
    ->send();
$camp = $response->body;

$url = $baseUrl."?a=campqueue&id=$id";
var_dump($url);
$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($username, $password)
    ->send();
$tasks = $response->body;



echo "<h2>".$camp->name." (".$camp->campId.")</h2>";
echo "<p>b1: ".$camp->b1."&nbsp;|&nbsp;";
echo "b2: ".$camp->b2."&nbsp;|&nbsp;";
echo "b3: ".$camp->b3."&nbsp;|&nbsp;";
echo "p1: ".$camp->p1."&nbsp;|&nbsp;";
echo "p2: ".$camp->p2."&nbsp;|&nbsp;";
echo "scores: ".$camp->scores."</p>";

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
		echo "<li>id: ".$b->buildingId.", type: ".$b->type.", level: ".$b->level."</li>";
	}
	?>
		</ul>
	</td>
	<td>
	<ul>
	<?php
	foreach ($tasks as $t) {
		echo "<li>id: ".$t->taskId.", finishedAt: ".$t->finishedAt.", type: ".$t->type.", oid1: ".$t->objectId1.", oid2: ".$t->objectId2.", level: ".$t->level."</li>";
	}
	?>
	</ul>
	</td>
</tr>
</table>

