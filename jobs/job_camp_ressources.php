<?php
/**
 * distribute camp ressources based on camp buildings and fields
 */


require_once("../config.inc.php");
require_once("../includes.inc.php");
$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);

$repository->resetLeaderboard();

$camps = $repository->getCamps();
foreach($camps as $camp) {

	// todo: die ganze logik der ressourcenproduktion
	// grundsatzfrage: wird ein task dafür erstellt? 
	// damit sicher gestellt ist, dass alle 15 min ausgeschüttet wird
	// was ist, wenn der job stundenlang ausgefallen war? und dann wieder anstartet?

	$camp->b1 += $camp->properties->pB1;
	$camp->b1 = $camp->b1 > $camp->properties->sB1 ? $camp->properties->sB1 : $camp->b1;
	$camp->b2 += $camp->properties->pB2;
	$camp->b2 = $camp->b2 > $camp->properties->sB2 ? $camp->properties->sB2 : $camp->b2;
	$camp->b3 += $camp->properties->pB3;
	$camp->b3 = $camp->b3 > $camp->properties->sB3 ? $camp->properties->sB3 : $camp->b3;
	$camp = $repository->updateCamp($camp);
}

?>