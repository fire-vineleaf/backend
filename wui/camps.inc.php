<h2>camps</h2>
<ul>
<?php

$url = $baseUrl."?a=camps&id=343";
var_dump($url);

$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($username, $password)
    ->send();


$camps = $response->body;

foreach($camps as $camp) {
	echo "<li><a href='index.php?page=camp&id=".$camp->campId."'>".$camp->name."</a></li>";
}
?>
</ul>