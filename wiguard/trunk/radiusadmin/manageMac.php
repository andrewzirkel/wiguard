<?php 
include "auth/checkLevel2.php";
include "./conf.php";
include "./functions.php";

function printRow($id,$ETHMAC,$WiMAC,$ComputerName,$sn,$editFlag) {
	if ($editFlag) {
		printf("<tr bgcolor=red><td><input type=\"text\" name=\"ETHMAC\" value=\"%s\"></td><td><input type=\"text\" name=\"WiMAC\" value=\"%s\"</td><td><input type=\"text\" name=\"ComputerName\" value=\"%s\"</td><td><input type=\"text\" name=\"sn\" value=\"%s\"</td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='1'\"><input type=\"Submit\" class=\"button\" value=\"Remove\" OnClick=\"this.form.remove.value='1'\"><input type=hidden name=id value=%d></td></tr>\n",$ETHMAC,$WiMAC,$ComputerName,$sn,$id);
	} else {
		printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.create.value='%d'\"></td></tr>\n",$ETHMAC,$WiMAC,$ComputerName,$sn,$id);
	}
}

function search($text,$target) {
	include "./conf.php";
	$result = validateMac($text);
	if ($result) { //search text is not mac address
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '%$text%' ORDER BY ComputerName";
		$result = mysql_query($query) or die("$query -" . mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			if ($target == $row['id']) printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],TRUE);
			else printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],FALSE);
			//printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",$row['ETHMAC'],$row['WiMAC'],$row['ComputerName']);
		}
		mysql_free_result($result);	
	}
	else {
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC Like '$text' OR WiMAC like '$text' ORDER BY ComputerName";
		$result = mysql_query($query) or die(mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			if ($id == $row['id']) printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],TRUE);
			else printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],FALSE);
			//printf("<tr><td>%s</td><td></td><td>%s</td></tr>",$row['MACAddress'],$row['ComputerName']);
		}
		mysql_free_result($result);
	}
}

$create = $_POST['create'];
if ($_POST['remove']) {
	printf("%s<br>",deleteComputer($_POST['ETHMAC'],$_POST['WiMAC'],$_POST['ComputerName'],$_POST['sn'],$_POST['id']));
	$create = "";
}
if ($create == "1") {
	printf("%s<br>",addComputer($_POST['ETHMAC'],$_POST['WiMAC'],$_POST['ComputerName'],$_POST['sn'],$_POST['id']));
	$create = "";
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
<input type=hidden name=remove value=0>
<input type=hidden name=add value=0>
EOM;
if ($create) printf("<input type=hidden name=create value=\"%s\">",$create);
else printf("<input type=hidden name=create value=0>");

if ($searchText) echo "<input type=text name=searchText value=\"$searchText\">";
else echo "<input type=text name=searchText>";
echo "<input type=\"Submit\" class=\"button\" value=\"Search\">";
if (!$create && !$_POST['add']) printf("<input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.add.value='1'\">");
?>
<br><br>
<table><tr><td><a href=list.php>Simple Listing of Computers</a></td>
<td><a href=addMac.php target="right">Batch Add Computers</a></td>
<td><a href=delete.php target="right">Batch Delete Computers</a></td></tr></table>
<table border=1>
<tr><th>ETH MAC</th><th>Wi MAC</th><th>Name</th><th>Serial</th><th></th></tr><br>
<?php
if ($_POST['add']=='1') printf("<tr bgcolor=red><td><input type=\"text\" name=\"ETHMAC\"></td><td><input type=\"text\" name=\"WiMAC\"</td><td><input type=\"text\" name=\"ComputerName\"></td><td><input type=\"text\" name=\"sn\"></td><td><input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.create.value='1'\"><input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.create.value=''\">\n");
if($searchText) search($searchText,$create);
else search(NULL,$create);
echo "</table>";
if ($create || $_POST['add']) {
	printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.create.value=''\">");
} else printf("<input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.add.value='1'\">");
?>
</form>
<table><tr><td><a href=list.php>Simple Listing of Computers</a></td>
<td><a href=addMac.php target="right">Batch Add Computers</a></td>
<td><a href=delete.php target="right">Batch Delete Computers</a></td></tr></table>
</body>
</html>