<?php 
include "auth/checkLevel2.php";
include "./conf.php";
include "./functions.php";

function printRow($id,$ETHMAC,$WiMAC,$ComputerName,$sn,$gp,$editFlag) {
	if ($editFlag) {
		printf("<tr bgcolor=red><td><input type=\"text\" name=\"ETHMAC\" value=\"%s\"></td><td><input type=\"text\" name=\"WiMAC\" value=\"%s\"></td><td><input type=\"text\" name=\"ComputerName\" value=\"%s\"></td><td><input type=\"text\" name=\"sn\" value=\"%s\"></td><td><input type=\"text\" name=\"gp\" value=\"%s\"></td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='1'\"><input type=\"Submit\" class=\"button\" value=\"Remove\" OnClick=\"this.form.remove.value='1'\"><input type=hidden name=id value=%d></td></tr>\n",$ETHMAC,$WiMAC,$ComputerName,$sn,$gp,$id);
	} else {
		printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.create.value='%d'\"></td></tr>\n",$ETHMAC,$WiMAC,$ComputerName,$sn,$gp,$id);
	}
}

function search($text,$target) {
	include "./conf.php";
	$result = validateMac($text);
	if ($result) { //search text is not mac address
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '%$text%' OR sn LIKE '%$text%' ORDER BY ComputerName";
		$result = mysqli_query($query) or die("$query -" . mysqli_error());
		while ($row = mysqli_fetch_assoc($result)) {
			if ($target == $row['id']) printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],$row['filter-id'],TRUE);
			else printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],$row['filter-id'],FALSE);
			//printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",$row['ETHMAC'],$row['WiMAC'],$row['ComputerName']);
		}
		mysqli_free_result($result);	
	}
	else {
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC Like '$text' OR WiMAC like '$text' ORDER BY ComputerName";
		$result = mysqli_query($query) or die(mysqli_error());
		while ($row = mysqli_fetch_assoc($result)) {
			if ($target == $row['id']) printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],$row['filter-id'],TRUE);
			else printRow($row['id'],$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],$row['filter-id'],FALSE);
			//printf("<tr><td>%s</td><td></td><td>%s</td></tr>",$row['MACAddress'],$row['ComputerName']);
		}
		mysqli_free_result($result);
	}
}

if (isset($_POST['create'])) $create = $_POST['create']; else $create = "";
if (isset($_POST['remove']) && $_POST['remove']) {
	printf("%s<br>",deleteComputer($_POST['ETHMAC'],$_POST['WiMAC'],$_POST['ComputerName'],$_POST['sn'],$_POST['gp'],$_POST['id']));
	$create = "";
}
if (isset($create) && $create == "1") {
	if (isset($_POST['id'])) printf("%s<br>",addComputer($_POST['ETHMAC'],$_POST['WiMAC'],$_POST['ComputerName'],$_POST['sn'],$_POST['gp'],$_POST['id']));
	else printf("%s<br>",addComputer($_POST['ETHMAC'],$_POST['WiMAC'],$_POST['ComputerName'],$_POST['sn'],$_POST['gp']));
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

if (isset($_POST['searchText'])) $searchText = $_POST['searchText']; else $searchText="";
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
if (!$create && !(isset($_POST['add']) && $_POST['add'])) printf("<input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.add.value='1'\">");
?>
<br><br>
<table><tr><td><a href=list.php>Simple Listing of Computers</a></td>
<td><a href=addMac.php target="right">Batch Add Computers</a></td>
<td><a href=delete.php target="right">Batch Delete Computers</a></td></tr></table>
<table border=1>
<tr><th>ETH MAC</th><th>Wi MAC</th><th>Name</th><th>Serial</th><th>Group Policy</th><th></th></tr><br>
<?php
if (isset($_POST['add']) && $_POST['add']) printf("<tr bgcolor=red><td><input type=\"text\" name=\"ETHMAC\"></td><td><input type=\"text\" name=\"WiMAC\"</td><td><input type=\"text\" name=\"ComputerName\"></td><td><input type=\"text\" name=\"sn\"></td><td><input type=\"text\" name=\"gp\"</td><td><input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.create.value='1'\"><input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.create.value=''\">\n");
if($searchText) search($searchText,$create);
else search(NULL,$create);
echo "</table>";
if ($create || (isset($_POST['add']) && $_POST['add'])) {
	printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.create.value=''\">");
} else printf("<input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.add.value='1'\">");
?>
</form>
<table><tr><td><a href=list.php>Simple Listing of Computers</a></td>
<td><a href=addMac.php target="right">Batch Add Computers</a></td>
<td><a href=delete.php target="right">Batch Delete Computers</a></td></tr></table>
</body>
</html>
