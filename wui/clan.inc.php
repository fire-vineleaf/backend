<?php
if (isset($_GET["status"])) {
	$status = $_GET["status"];
	$c1 = $_GET["id"];
	$service->setDiplomacy($c1, $status);
}

if (isset($_POST["playerId"])) {
	$service->invitePlayer($_POST["playerId"]);
}
if (isset($_POST["name"])) {
	$name = $_POST["name"];
	$clan = new Clan();
	$clan->name = $name;
	$service->createClan($clan);
}
if (isset($_GET["a"])) {
	$action = $_GET["a"];
	switch ($action) {
		case "accept":
		$id = $_GET["id"];
		$service->acceptInvitation($id);
		break;
		case "accept":
		$id = $_GET["id"];
		$service->acceptInvitation($id);
		break;
		case "leaveclan":
		$service->leaveClan();
		break;
		case "disbandclan":
		$service->disbandClan();
		break;
	}
}
if (isset($_GET["id"])) {
	$id = $_GET["id"];
	$hasClan = true;
	$ownClan = false;
	$clan = $service->getClan($id);
} else {
	$id = null;
	$ownClan = true;
	$hasClan = !is_null($contextPlayer->clanId);
	if ($hasClan) {
		$clan = $service->getClan(null);
	}
}
?>
<?php if (!$hasClan) { ?>
<h1>no clan</h1>
<?php } ?>

<?php if ($hasClan) { ?>
<h1>Clan: <?php echo $clan->name; ?></h1>
<div class="row">
	<div class="col-md-2">
	<h2>Members</h2>
	<ul>
	<?php
	$members = $service->getClanMembers($id);
	foreach($members as $member) {
		?>
		<li>
		<?php echoPlayer($member); ?>
		</li>

		<?php
	}
	?>
	</ul>
	</div>
	<?php if ($ownClan) { ?>
	<div class="col-md-2">
	<h2>Clan Invitations</h2>
	<ul>
	<?php
	$invitations = $service->getClanInvitations(null);
	foreach($invitations as $invitation) {
		?>
		<li>
		<?php echoPlayer($invitation->player); ?>
		</li>

		<?php
	}
	?>
	</ul>
	<h3>Invite</h3>
	<form method="post">
	Player: <?php echoSelectPlayer(); ?>
	<input type="submit" value="invite">
	</form>
	</div>
	<?php } ?>
	<?php if ($ownClan) { ?>

	<div class="col-md-2">
	<h2>Clan Applications</h2>
	</div>
	<?php } ?>
	<?php if ($ownClan) { ?>
	<div class="col-md-3">
	<h2>Feed</h2>
	<ul>
	<?php
	$items = $service->getClanFeedItems();
	foreach($items as $item) {
		?>
		<li>
		<?php echoDate($item->createdAt); ?> - <?php echoFeedItemType($item->type); ?>
		</li>

		<?php
	}
	?>
	</ul>
	</div>
	<?php } ?>
	
	
</div><!--row-->
	<?php } ?>
<div class="row">
	<?php if ($ownClan) { ?>
	<div class="col-md-2">
	<h2>My Invitations</h2>
	<ul>
	<?php
	$invitations = $service->getPlayerInvitations();
	foreach($invitations as $invitation) {
		?>
		<li>
		<?php echoClan($invitation->clan); ?> - <a href="index.php?page=clan&a=accept&id=<?php echo $invitation->invitationId; ?>">Accept</a> | <a href="index.php?page=clan&a=reject&id=<?php echo $invitation->invitationId; ?>">Reject</a>
		</li>

		<?php
	}
	?>
	</ul>
	</div>
	<?php } ?>
	<?php if ($ownClan) { ?>
	<div class="col-md-2">
	<h2>Membership</h2>
	<p><a href="index.php?page=clan&a=leaveclan">Leave Clan</a></p>
	<p><a href="index.php?page=clan&a=disbandclan">Disband Clan</a></p>
	</div>
	<?php } ?>
	<?php if (!$ownClan && $hasClan) { ?>
	<div class="col-md-2">
	<h2>Diplomacy Status</h2>
	<?php echo $clan->status; ?>
	
	<p><a href="index.php?page=clan&id=<?php echo $id; ?>&status=0">Set to Neutral</a><br/>
	<a href="index.php?page=clan&id=<?php echo $id; ?>&status=1">Set to Enemy</a><br/>
	<a href="index.php?page=clan&id=<?php echo $id; ?>&status=2">Set to Ally</a><br/>
	<a href="index.php?page=clan&id=<?php echo $id; ?>&status=3">Set to Non-Aggression Pact</a><br/>
	<a href="index.php?page=clan&id=<?php echo $id; ?>&status=4">Set to Vassal</a></p>

	</div>
	<?php } ?>
	<?php if ($ownClan && $hasClan) { ?>
	<div class="col-md-3">
	<h2>Clan Diplomacy</h2>
	<ul>
	<?php
	$clans = $service->getClanDiplomacy();
	foreach($clans as $clan) {
		echo "<li>";
		echoClan($clan);
		echo "</li>";
	}
	?>
	</ul>
	</div>
	<?php } ?>
	
	<?php if (!$hasClan) { ?>
	<div class="col-md-3">
	<h2>new Clan</h2>
	<form method="post">
	Name: <input type="text" name="name"><br/>
	<input type="submit" value="create new clan">
	</form>
	</div>	<?php } ?>
	
</div>
	
	
	
	