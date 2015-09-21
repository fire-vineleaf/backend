<?php
$dirName = dirname(__FILE__);

require($dirName."/gameconfig.inc.php");

/* core */
require($dirName."/Core/BaseManager.php");
require($dirName."/Core/SecurityManager.php");

require($dirName."/Core/repository.php");
require($dirName."/Core/model.php");

require($dirName."/Core/service.php");
require($dirName."/Core/ZSAService.php");

/* REST API */
require($dirName."/WebApi/ZSAApiController.php");

?>