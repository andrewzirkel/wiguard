<?php
include "./conf.php";
include "./functions.php";
$mac=trim($_GET['mac']);
if ($mac != "" ) {
	$result = queryName($mac);
	if ($result == "" ) echo 0; else echo $result;
} else echo 0;

?>