<h1>messages</h1>
<ol>
<?php
if (isset($_POST["subject"])) {
	$message = new Message();
	$message->participants[] = $_POST["to"];
	$message->subject = $_POST["subject"];
	$message->content = $_POST["content"];
	$message = $service->createMessage($message);
}

$messages = $service->getMessages();
foreach ($messages as $m) {
	echo "<li><a href='index.php?page=message&id=".$m->messageId."'>".$m->subject."</a>(isRead: ".$m->isRead.")<br/>".date(DATE_RFC822, $m->createdAt)." - <a href='index.php?page=user&id=".$m->createdBy."'>".$m->createdByPlayer->name."</a></li>";
}
?>
</ol>

<form method="post">
<p>to: <select name="to"><?php
for ($i=1;$i<= 200;$i++) {
	echo "<option value=\"$i\">Player$i</option>";
}
?></select></p>
<p>subject: <input type="text" name="subject"></p>
<p>content: <textarea name="content"></textarea></p>
<input type="submit" value="send">
</form>