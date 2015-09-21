<?php

session_start();


if (isset($_GET["username"])) {
	$username = $_GET["username"];
	$password = "hallo";
	$_SESSION['username'] = $username;
	$_SESSION['password'] = $password;
}
if (isset($_SESSION["username"])) {
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];
}

require("../config.inc.php.conf");
require("../includes.inc.php");
require("../init.inc.php");
require("httpful.phar");

$baseUrl = "http://localhost/zsa/api.php";

?><!DOCTYPE html>
<html lang="en">
  <head>
  <title>ZSA WUI</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
<script type="text/javascript" src="../WebClient/jquery.min.js"></script>
<script type="text/javascript" src="../WebClient/jquery-ui.min.js"></script>
<script type="text/javascript" src="../WebClient/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="../WebClient/jquery-ui.css" />
<link rel="stylesheet" href="../WebClient/bootstrap-3.3.5-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../WebClient/bootstrap-3.3.5-dist/css/bootstrap-theme.css">
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
      <a class="navbar-brand" href="#">ZSA WUI</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Username: <?php echo $username; ?></a></li>
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






<h1>ZSA WUI</h1>
<p><b>WebAPI:</b>&nbsp;
<a href="index.php">camps</a>&nbsp;|&nbsp;
<a href="index.php?page=field">field</a>&nbsp;|&nbsp;
<a href="index.php?page=messages">messages</a>&nbsp;|&nbsp;
<b>Internal:</b>&nbsp;
<a href="index.php?page=queue">queue</a>&nbsp;|&nbsp;
</p>

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