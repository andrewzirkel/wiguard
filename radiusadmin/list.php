<?php include "auth/checkLevel1.php"?>

<html>
<head>
<title>Users in database</title>
<link rel="stylesheet" href=style.css>
</head>
<body style="font-family:Courier">
<center>Simple listing of computers currently in database</center>
<br>
<?php
include "./conf.php";
/*
$query="SELECT * FROM $wgdb.computers ORDER BY $wgdb.computers.ComputerName";
$result=mysqli_query($query) or die(mysqli_error());
while ($row = mysqli_fetch_assoc($result)) {
	echo $row['ETHMAC'];
	echo ",";
	echo $row['WiMAC'];
	echo ",";
	echo $row['ComputerName'];
	echo "<br>";
}
mysqli_free_result($result);
*/
//$query="SELECT $radb.radcheck.UserName, $wgdb.computername.ComputerName FROM $radb.radcheck LEFT OUTER JOIN $wgdb.computername ON $radb.radcheck.UserName = $wgdb.computername.MACAddress ORDER BY $wgdb.computername.ComputerName";
//$query="SELECT $radb.radcheck.UserName, $wgdb.computers.ComputerName FROM $radb.radcheck LEFT OUTER JOIN $wgdb.computers ON $radb.radcheck.UserName = $wgdb.computers.ETHMAC OR $radb.radcheck.UserName = $wgdb.computers.WiMAC ORDER BY $wgdb.computers.ComputerName";
//List computers table
$query="SELECT * from $wgdb.computers ORDER BY $wgdb.computers.ComputerName";
$result=$mysqli->query($query) or die($mysqli->error);
while ($row = mysqli_fetch_assoc($result)) {
	printf("%s,%s,%s,%s,%s<br>",$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],$row['filter-id']);
}
mysqli_free_result($result);
//List computername table
$query = "SELECT * from $wgdb.computername ORDER BY ComputerName";
$result=$mysqli->query($query) or die($mysqli->error);
while ($row = mysqli_fetch_assoc($result)) {
	printf("%s,%s<br>",$row['MACAddress'],$row['ComputerName']);
}
mysqli_free_result($result);
//List macs in radius not in computers table
$query="SELECT $radb.radcheck.UserName FROM $radb.radcheck WHERE $radb.radcheck.UserName NOT IN 
					(SELECT ETHMAC FROM $wgdb.computers) AND $radb.radcheck.UserName NOT IN 
					(SELECT WiMAC FROM $wgdb.computers) AND $radb.radcheck.UserName NOT IN
					(SELECT MACAddress FROM $wgdb.computername) ORDER BY $radb.radcheck.UserName";
$result=$mysqli->query($query) or die($mysqli->error);
while ($row = mysqli_fetch_assoc($result)) {
	printf("%s<br>",$row['UserName']);
}
mysqli_free_result($result);
?>
</body>
</html>