<?php
$id = $_GET["id"];
$service = new ZSAService($contextUser, $repository);
$m = $service->getMessage($id);
?>

<h1><?php echo $m->subject; ?></h1>

