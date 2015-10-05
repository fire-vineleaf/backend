<?php
$id = $_GET["id"];

$url = $baseUrl."?a=camp&id=$id";
var_dump($url);
$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($email, $password)
    ->send();
$camp = $response->body;


echo "<h2>Camp: ".$camp->name." (".$camp->campId.")</h2>";
echo "<p>Player: ";
echoPlayer($camp->player);
echo "</p>";

echo "Show in Map: ";
echo echoCamp($camp);
?>

<h2>Actions</h2>
<p><a href="#">Attack</a></p>
<p><a href="#">Defend</a></p>
<p><a href="#">Send</a></p>
