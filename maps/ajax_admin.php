<?php
require_once('config.php');
require_once(INC_DIR.'csAppMapAdmin.php');

$csapp = new csAppMapAdmin();
$csapp -> doit();

?>