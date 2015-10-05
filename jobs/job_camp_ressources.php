<?php
/**
 * distribute camp ressources based on camp buildings and fields
 */


require("../config.inc.php");
require("../includes.inc.php");
$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);

$repository->resetLeaderboard();

$camps = $repository->getCamps();
foreach($camps as $camp) {

	// todo: die ganze logik der ressourcenproduktion
	// grundsatzfrage: wird ein task dafür erstellt? 
	// damit sicher gestellt ist, dass alle 15 min ausgeschüttet wird
	// was ist, wenn der job stundenlang ausgefallen war? und dann wieder anstartet?

	$camp->b1++;
	$camp->b2++;
	$camp->b3++;
	$camp = $repository->updateCamp($camp);
}

?>
done