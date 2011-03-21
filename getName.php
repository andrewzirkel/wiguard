<?php
include "./conf.php";
include "./functions.php";
$mac=$_GET['mac'];
if ($mac != "" ) {
	#mysql_connect(localhost,$user,$password);
	#@mysql_select_db($database) or die("Unable to select database");
	$result = queryComputer($mac);
	if ($result == "" ) echo 0; else echo $result;
} else echo 0;

?>