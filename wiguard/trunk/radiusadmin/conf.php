<?php
$version="2.4b";
$radb="radius";
$wgdb="wiguard";
$dbuser="wiguard";
$dbpass="wiguard";
$debug=FALSE;

$groupDelim='-';
$groupDefault='staff';

$procs=array("apache","radius","mysql");

mysql_connect("localhost",$dbuser,$dbpass);
/*$radbp=mysql_connect(localhost,$user,$password,true);
mysql_select_db($radb,$radbp) or die("Unable to select database");
$wgdbp=mysql_connect(localhost,$user,$password,true);
mysql_select_db($wgdb,$wgdbp) or die("Unable to select database");
*/
?>