<h1>messages</h1>
<ol>
<?php
if (isset($_POST["subject"])) {
	$message = new Message();
	$message->participants[] = $_POST["playerId"];
	$message->subject = $_POST["subject"];
	$message->content = $_POST["content"];
	$message = $service->createMessage($message);
}

$messages = $service->getMessages();
foreach ($messages as $m) {
	echo "<li><a href='index.php?page=message&id=".$m->messageId."'>".$m->subject."</a>(isRead: ".$m->isRead.")<br/>";
	echoDate($m->createdAt);
	echo " - ";
	echoPlayer($m->createdByPlayer);
	echo "</li>";
}
?>
</ol>
<h2>new message</h2>
<form method="post">
<p>to: <?php echoSelectPlayer(); ?></p>
<p>subject: <input type="text" name="subject"></p>
<p>content: <textarea name="content"></textarea></p>
<input type="submit" value="send">
</form>