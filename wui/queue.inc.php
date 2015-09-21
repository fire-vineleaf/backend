<h2>queue</h2>
<ul>
<?php

$service = new ZSAService($contextUser, $repository);
$tasks = $repository->getDueTasks();

foreach ($tasks as $t) {
	echo "<li>id: ".$t->taskId.", finishedAt: ".$t->finishedAt.", type: ".$t->type.", oid1: ".$t->objectId1.", oid2: ".$t->objectId2.", level: ".$t->level."</li>";
}
?>
</ul>