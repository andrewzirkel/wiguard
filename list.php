<?php include "auth/checkLevel1.php"?>

<html>
<head>
<title>Users in database</title>
<link rel="stylesheet" href=style.css>
</head>
<body style="font-family:Courier">
<center>Simple listing of mac address currently in database</center>
<?php
include "./conf.php";
#mysql_connect(localhost,$user,$password);
#@mysql_select_db($wgdb) or die("Unable to select database");
$query="SELECT $radb.radcheck.UserName, $wgdb.computername.ComputerName FROM $radb.radcheck LEFT OUTER JOIN $wgdb.computername ON $radb.radcheck.UserName = $wgdb.computername.MACAddress ORDER BY $wgdb.computername.ComputerName";
$result=mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
	echo $row['UserName'];
	echo ",";
	echo $row['ComputerName'];
	echo "<br>";
}
?>
</body>
</html>
