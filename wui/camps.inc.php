<h2>camps</h2>
<ul>
<?php

$url = $baseUrl."?a=camps";
var_dump($url);

$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($email, $password)
    ->send();

$camps = $response->body;

foreach($camps as $camp) {
	echo "<li><a href='index.php?page=camp&id=".$camp->campId."'>".$camp->name."</a> - <a href='index.php?page=field&x=".$camp->x."&y=".$camp->y."'>Show in Map</a></li>";
}
?>
</ul>