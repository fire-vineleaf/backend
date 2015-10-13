<?php

function echoPlayer($player) {
	echo "<a href=\"index.php?page=player&id=".$player->playerId."\">".$player->name." (".$player->points.")</a>";
}

function echoClan($clan) {
	echo DiplomacyStatus::$labels[$clan->status]." <a href=\"index.php?page=clan&id=".$clan->clanId."\">".$clan->name." (".$clan->points.")</a>";
}
function echoCamp($camp) {
	echo "<a href='index.php?page=field&x=".$camp->x."&y=".$camp->y."'>".$camp->name." (".$camp->points.")</a>";
}
function echoBuilding($building) {
	echo "<a href='index.php?page=building&id=".$building->buildingId."'>".$building->type.":".getBuildingName($building->type)." (Level ".$building->level.")</a>";
}

function getBuildingName($type) {
	switch ($type) {
		case 0:
			return "Keep";
		break;
		case 1:
			return "Baracks";
		break;
		case 4:
			return "Farm";
		break;
		case 5:
			return "Library";
		break;
		case 6:
			return "Fortifications";
		break;
		case 10:
			return "ProducerB1";
		break;
		case 11:
			return "ProducerB2";
		break;
		case 12:
			return "ProducerB3";
		break;
		case 7:
			return "StoreB1";
		break;
		case 8:
			return "StoreB2";
		break;
		case 9:
			return "StoreB3";
		break;
		default:
		return "Type $type";
		break;
	}
}

function echoSelectPlayer() {
	echo "<select name=\"playerId\">";
	for ($i=1;$i<= 200;$i++) {
		echo "<option value=\"$i\">Player$i</option>";
	}
	echo "</select>";
}

function echoDate($timestamp) {
	echo date(DATE_RFC822, $timestamp);
}

function echoFeedItemType($type) {
	switch($type) {
		case "1":
		echo "InvitationSent";
		break;
		case "2":
		echo "InvitationAccepted";
		break;
		case "3":
		echo "InvitationRejected";
		break;
		case "4":
		echo "ApplicationSent";
		break;
		case "5":
		echo "ApplicationAccepted";
		break;
		case "6":
		echo "ApplicationRejected";
		break;
		case "7":
		echo "JoinedClan";
		break;
		case "8":
		echo "RightRevoked";
		break;
		case "9":
		echo "RightGranted";
		break;
		case "10":
		echo "LeftClan";
		break;
		case "11":
		echo "CreatedClan";
		break;
		case "12":
		echo "DiplomacyChanged";
		break;


		default:
		echo "todo: map me";
		break;
	}
}
/*
	const _INVITE = 1;
	const _MASSMAIL = 2;
	const _MODERATOR = 4;
	const _DIPLOMACY = 8;
	const _DISMISS = 16;
	const _RIGHTS = 32;
	const _DISBAND = 64;
	*/
function echoPlayerRights($player, $isEnabled) {
	global $service;
	$disabled = $isEnabled ? "" : "disabled";
	$checked = $service->hasRight($player->rights, Rights::_INVITE) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_INVITE."\" $checked $disabled> Invite<br/>";
	$checked = $service->hasRight($player->rights, Rights::_MASSMAIL) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_MASSMAIL."\" $checked $disabled> Massmail<br/>";
	$checked = $service->hasRight($player->rights, Rights::_MODERATOR) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_MODERATOR."\" $checked $disabled> Moderator<br/>";
	$checked = $service->hasRight($player->rights, Rights::_DIPLOMACY) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_DIPLOMACY."\" $checked $disabled> Diplomacy<br/>";
	$checked = $service->hasRight($player->rights, Rights::_DISMISS) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_DISMISS."\" $checked $disabled> Dismiss<br/>";
	$checked = $service->hasRight($player->rights, Rights::_RIGHTS) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_RIGHTS."\" $checked $disabled> Rights<br/>";
	$checked = $service->hasRight($player->rights, Rights::_DISBAND) ? "checked" : ""; 
	echo "<input type=\"checkbox\" value=\"".Rights::_DISBAND."\" $checked $disabled> Disband<br/>";

}

session_start();
if (isset($_GET["email"])) {
	$email = $_GET["email"];
	$password = "hallo";
	$_SESSION['email'] = $email;
	$_SESSION['password'] = $password;
}
if (isset($_SESSION["email"])) {
	$email = $_SESSION['email'];
	$password = $_SESSION['password'];
}

require("../config.inc.php");
require("../gameconfig.inc.php");
require("../includes.inc.php");
require("../init.inc.php");
require("httpful.phar");

$baseUrl = "http://localhost/vineleaf/backend/api.php";

$securityManager = new SecurityManager($repository);
$contextPlayer = $securityManager->getPlayerById($contextAccount->playerId);
$service = new ZSAService($contextPlayer, $repository);
$service->config = $config;
?><!DOCTYPE html>
<html lang="en">
  <head>
  <title><?php echo $contextPlayer->playerId; ?> - <?php echo $contextPlayer->clanId; ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript" src="jquery-ui.min.js"></script>
<script type="text/javascript" src="bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="jquery-ui.css" />
<link rel="stylesheet" href="bootstrap-3.3.5-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="bootstrap-3.3.5-dist/css/bootstrap-theme.css">
</head>
<body>


<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">vineleaf testclient</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="index.php">Camps</a></li>
        <li><a href="index.php?page=messages">Messages</a></li>
        <li><a href="index.php?page=clan">Clan</a></li>
        <li><a href="index.php?page=forum">Forum</a></li>
        <li><a href="index.php?page=field">Map</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="index.php?page=clans">Clans</a></li>
        <li><a href="index.php?page=players">Players</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Internal <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="index.php?page=queue">Queue</a></li>
            <li><a href="index.php?page=diplomacy">Diplomacy</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="index.php?page=gameconfig">Game Config</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="#"><?php echo $contextPlayer->name; ?> - <?php echo $email; ?></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<?php
if (isset($_GET["page"])) {
	$page = $_GET["page"];
} else {
	$page = "camps";
}
require("$page.inc.php");
?>

</body>
</html>