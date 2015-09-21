<h1>messages</h1>
<ol>
<?php

$service = new ZSAService($contextUser, $repository);
$messages = $service->getMessages();

foreach ($messages as $m) {
	echo "<li><a href='index.php?page=message&id=".$m->messageId."'>".$m->subject."</a><br/>".date(DATE_RFC822, $m->createdAt)." - <a href='index.php?page=user&id=".$m->createdBy."'>".$m->createdByUser->displayName."</a> isRead: ".$m->isRead."</li>";
}
?>
</ol>