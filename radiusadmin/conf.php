<?php
$version="2.5.3";
$radb="radius";
$wgdb="wiguard";
$dbuser="wiguard";
$dbpass="wiguard";
$debug=FALSE;

$groupDelim='-';
$groupDefault='staff';

$procs=array("apache","radius","mysql");

mysqli_connect("localhost",$dbuser,$dbpass);
/*$radbp=mysqli_connect(localhost,$user,$password,true);
mysqli_select_db($radb,$radbp) or die("Unable to select database");
$wgdbp=mysqli_connect(localhost,$user,$password,true);
mysqli_select_db($wgdb,$wgdbp) or die("Unable to select database");
*/
?>