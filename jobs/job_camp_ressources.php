<?php
/**
 * distribute camp ressources based on camp buildings and fields
 */

require_once("../config.inc.php");
require_once("../includes.inc.php");
$repository = new MySqlRepository($cfgMySqlHost, $cfgMySqlUserName, $cfgMySqlPassword, $cfgMySqlDatabase, $cfgMySqlPort);

$repository->resetLeaderboard();

$lastExecution = file_get_contents("ressources.txt");
if ($lastExecution == "") {
	file_put_contents("ressources.txt", time());
}
$delta = time() - $lastExecution;

$intervall = 15 * 1; // 15 mins

if ($delta > $intervall) {
	$num = floor($delta / $intervall);
	echo "ressources: execute $num times\n";
	for ($i=0;$i<$num;$i++) {	
		$camps = $repository->getCamps();
		foreach($camps as $camp) {

			// todo: die ganze logik der ressourcenproduktion
			// grundsatzfrage: wird ein task dafür erstellt? 
			// damit sicher gestellt ist, dass alle 15 min ausgeschüttet wird
			// was ist, wenn der job stundenlang ausgefallen war? und dann wieder anstartet?

			$camp->b1 += $camp->properties->pb1;
			$camp->b1 = $camp->b1 > $camp->properties->sb1 ? $camp->properties->sb1 : $camp->b1;
			$camp->b2 += $camp->properties->pb2;
			$camp->b2 = $camp->b2 > $camp->properties->sb2 ? $camp->properties->sb2 : $camp->b2;
			$camp->b3 += $camp->properties->pb3;
			$camp->b3 = $camp->b3 > $camp->properties->sb3 ? $camp->properties->sb3 : $camp->b3;
			$camp = $repository->updateCamp($camp);
		}
	}
	file_put_contents("ressources.txt", time());
}

?>