<?php
$version="2.5.5";
$radb="radius";
$wgdb="wiguard";
$dbuser="wiguard";
$dbpass="wiguard";
$debug=FALSE;
$proxy='';

$groupDelim='-';
$groupDefault='staff';

$procs=array("apache","radius","mysql");

//mysqli_connect("localhost",$dbuser,$dbpass);
$mysqli = new mysqli("localhost",$dbuser,$dbpass);
if ($mysqli->connect_error) {
	die('Connect Error (' . $mysqli->connect_errno . ') '
			. $mysqli->connect_error);
}
/*$radbp=mysqli_connect(localhost,$user,$password,true);
mysqli_select_db($radb,$radbp) or die("Unable to select database");
$wgdbp=mysqli_connect(localhost,$user,$password,true);
mysqli_select_db($wgdb,$wgdbp) or die("Unable to select database");
*/
?>