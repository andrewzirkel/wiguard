<?php 
include "auth/checkLevel2.php";
include "./conf.php";
include "./functions.php";

function printRow($ETHMAC,$WiMAC,$ComputerName,$editFlag) {
	if ($editFlag) {
		printf("<tr bgcolor=red><td>%s</td><td>%s</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='%s'\"><input type=\"Submit\" class=\"button\" value=\"Remove\" OnClick=\"this.form.remove.value='%s'\"",$ETHMAC,$WiMAC,$ComputerName,$ComputerName,$ComputerName);
	} else {
		printf("<tr><td>%s</td><td>%s</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.create.value='%s'\"></td></tr>",$ETHMAC,$WiMAC,$ComputerName,$ComputerName);
	}
}

function search($text,$target) {
	include "./conf.php";
	$result = validateMac($text);
	if ($result) { //search text is not mac address
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '%$text%'";
		$result = mysql_query($query) or die("$query -" . mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			if (strcmp($target,$row['ComputerName']) == 0) printRow($row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],TRUE);
			else printRow($row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],FALSE);
			//printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",$row['ETHMAC'],$row['WiMAC'],$row['ComputerName']);
		}
		mysql_free_result($result);	
/*We'll deal with this in the simple list
		//computername table
		$query = "SELECT * FROM $wgdb.computername WHERE ComputerName LIKE '%$text%'";
		$result = mysql_query($query) or die("$query -" . mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			printRow($row['MACAddress'],$NULL,$row['ComputerName']);
			//printf("<tr><td>%s</td><td></td><td>%s</td></tr>",$row['MACAddress'],$row['ComputerName']);
		}
		mysql_free_result($result);	
*/
	}
	else {
		//computers table
		$result = mysql_query("SELECT * FROM $wgdb.computers WHERE ETHMAC Like '$text' OR WiMAC like '$text'") or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			if ($target == $row['ComputerName']) printRow($row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],TRUE);
			else printRow($row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],FALSE);
			//printf("<tr><td>%s</td><td></td><td>%s</td></tr>",$row['MACAddress'],$row['ComputerName']);
		}
		mysql_free_result($result);
/*We'll deal with this in the simple list
		//computerame and radius db
		$result = mysql_query("SELECT $radb.radcheck.UserName, $wgdb.computername.ComputerName FROM $radb.radcheck LEFT OUTER JOIN $wgdb.computername ON $radb.radcheck.UserName = $wgdb.computername.MACAddress ORDER BY $wgdb.computername.ComputerName") or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			printRow($row['ETHMAC'],$row['WiMac'],$row['ComputerName']);
			//printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",$row['ETHMAC'],$row['WiMac'],$row['ComputerName']);
		}
		mysql_free_result($result);
*/
	}
}

?>

<html>
<head>
<title>Manage Computers</title>
<link rel="stylesheet" href=./style.css>
</head>
<body>
<?php

$searchText = $_POST['searchText'];
//Search Form
echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<input type=hidden name=remove value="0">
<input type=hidden name=add value="0">

EOM;
if (!$searchText) echo "<input type=text name=searchText value=\"\">";
else echo "<input type=text name=searchText value=\"$searchText\">";
echo "<input type=\"Submit\" class=\"button\" value=\"Search\" />";
echo "<table border=1>";
if($searchText) search($searchText,$_POST['create']);
else search(NULL,$_POST['create']);
echo "</table>";
if ($_POST['create'] || $_POST['add']) printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.create.value=''\">");
?>
</form>
<table><tr><td><a href=list.php>Show Legacy MACs</a></td>
<td><a href=addMac.php target="right">Batch Add MACs</a></td>
<td><a href=delete.php target="right">Batch Delete MACs</a></td></tr></table>
</body>
</html>
