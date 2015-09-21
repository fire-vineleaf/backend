<?php
$dirName = dirname(__FILE__);

require($dirName."/gameconfig.inc.php");

/* core */
require($dirName."/core/BaseManager.php");
require($dirName."/core/SecurityManager.php");

require($dirName."/core/repository.php");
require($dirName."/core/model.php");

require($dirName."/core/service.php");
require($dirName."/core/ZSAService.php");

/* REST API */
require($dirName."/webapi/ZSAApiController.php");

?>