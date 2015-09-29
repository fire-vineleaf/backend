<?php
if (isset($_POST["subject"])) {
	$subject = $_POST["subject"];
	$content = $_POST["content"];
	$thread = new Thread();
	$thread->subject = $subject;
	$thread->content = $content;
	$service->createThread($thread);
}

if (isset($_POST["id"])) {
	$id = $_POST["id"];
	$content = $_POST["content"];
	$post = new Post();
	$post->content = $content;
	$post = $service->createPost($id, $post);
}
?>
<div class="row">
	<div class="col-md-3">
	<h2>Forum</h2>
	<ul>
	<?php
	$items = $service->getThreads();
	foreach($items as $item) {
		?>
		<li>
		<b><a href="?page=forum&id=<?php echo $item->threadId; ?>"><?php echo $item->subject; ?></a></b><br><?php echoDate($item->createdAt); ?>
		</li>
		<?php
	}
	?>
	</ul>
	</div>
	<div class="col-md-3">
	<h2>Thread</h2>
	<?php
	if (isset($_GET["id"])) {
		$id = $_GET["id"];
		$posts = $service->getPosts($id);
		foreach($posts as $post) {
			?>
			<li>
				<p><?php echoPlayer($post->createdByPlayer); ?> - <?php echoDate($post->createdAt); ?></p>
				<pre><?php echo $post->content; ?></pre>
			</li>
			<?php
		}
	}
	if (isset($_GET["id"])) {
	?>
	<h3>reply</h3>
	<form method="post">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<textarea name="content"></textarea>
	<input type="submit" value="reply">
	</form>
	<?php } ?>
	</div>
	<div class="col-md-3">
	<h2>new Thread</h2>
	<form method="post">
	subject: <input type="text" name="subject"><br/>
	content: <textarea name="content"></textarea>
	
	<input type="submit" value="create">
	</form>
	</div>


</div>