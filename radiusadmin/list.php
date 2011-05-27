<?php include "auth/checkLevel1.php"?>

<html>
<head>
<title>Users in database</title>
<link rel="stylesheet" href=style.css>
</head>
<body style="font-family:Courier">
<center>Simple listing of mac address currently in legacy database</center>
<?php
include "./conf.php";
/*
$query="SELECT * FROM $wgdb.computers ORDER BY $wgdb.computers.ComputerName";
$result=mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	echo $row['ETHMAC'];
	echo ",";
	echo $row['WiMAC'];
	echo ",";
	echo $row['ComputerName'];
	echo "<br>";
}
mysql_free_result($result);
*/
$query="SELECT $radb.radcheck.UserName, $wgdb.computername.ComputerName FROM $radb.radcheck LEFT OUTER JOIN $wgdb.computername ON $radb.radcheck.UserName = $wgdb.computername.MACAddress ORDER BY $wgdb.computername.ComputerName";
//$query="SELECT $radb.radcheck.UserName, $wgdb.computers.ComputerName FROM $radb.radcheck LEFT OUTER JOIN $wgdb.computers ON $radb.radcheck.UserName = $wgdb.computers.ETHMAC OR $radb.radcheck.UserName = $wgdb.computers.WiMAC ORDER BY $wgdb.computers.ComputerName";
$result=mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	echo $row['UserName'];
	echo ",";
	echo $row['ComputerName'];
	echo "<br>";
}
mysql_free_result($result);
?>
</body>
</html>