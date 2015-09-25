<?php

$id = $_GET["id"];

if (isset($_POST["reply"])) {
	$replyText = $_POST["reply"];
	
	$reply = new Reply();
	$reply->reply = $replyText;
	$service->replyToMessage($id, $reply);
	
}

$m = $service->getMessage($id);
?>
<h1><?php echo $m->subject; ?></h1>
<ul>
<?php
$replies = $service->getReplies($id);
foreach($replies as $reply) {
	?>
	<li>
	<b><?php echo $reply->createdByPlayer->name; ?> - <?php echo $reply->createdAt; ?></b><br/>
	<?php echo $reply->reply; ?>
	</li>

	<?php
}
?>
</ul>
<form method="post">
<textarea name="reply"></textarea>
<input type="submit" value="reply">
</form>
