<?php

function validateMac($mac) {
	include "./conf.php";
	$d = "[0-9a-f]";
	$message = "";
	if (!preg_match("/^[0-9a-f]{12}$/",$mac)){
		if(preg_match("/[A-F]/",$mac)){
			return("<strong>$mac</strong> Error: MAC address cannot have CAPITAL letters.");
		}else return("<strong>$mac</strong> Error: MAC Formatted incorrectly");
		return("<strong>$mac</strong> Error: MAC formatted incorrectly (Should not get here)");
	}
	return("");
}

function validateIP($IP) {
	include "./conf.php";
	$message = "";
	if (!preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ip)) {
	return("<strong>$IP</strong> Error: Invalid IP Address");
            }
}


function queryMac($mac) {
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return("$mac already exists in radcheck.  ");
}

function addMac($mac) {
	include "./conf.php";
	$result = validateMac($mac);
	if ($result) return($result);
	$result = queryMac($mac);
	if ($result) return($result);
	#what id to use:
	$result = mysql_query("SELECT MAX(id) FROM $radb.radcheck") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$id = $row['MAX(id)']+1;
	mysql_query("INSERT INTO $radb.radcheck VALUES('$id','$mac','Password','==','$mac')") or die(mysql_error());
	return("$mac Added to radcheck.  ");
}

function deleteMac($mac) {
	include "./conf.php";
	@mysql_select_db($radb) or die("Unable to select database");
	mysql_query("DELETE FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die(mysql_error());
	return("$mac deleted from radcheck.  ");
}

function queryComputerName($mac) {
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["ComputerName"]);
}

function addComputerName($mac,$name) {
	include "./conf.php";
	if (queryComputerName($mac) == "") {
		addmac($mac);
		mysql_query("REPLACE INTO $wgdb.computername VALUES('$mac','$name')") or die(mysql_error());
		return("Legacy $name added.  ");
	} else {
		mysql_query("REPLACE INTO $wgdb.computername VALUES('$mac','$name')") or die(mysql_error());
		return("Legacy $name updated.  ");
	}
}

function deleteComputerName($mac) {
	include "./conf.php";
	$result = validateMac($mac);
	if ($result != "") return($result);	
	//remove from raddb
	deleteMac($mac);
	mysql_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die(mysql_error());
	if (mysql_affected_rows() == 1) return("$mac deleted.  ");
	else return("ERROR: $mac in ComputerNames db");
}

//returns # of rows deleted
function cleanComputerName($name) {
	include "./conf.php";
	$count = 0;
	$query = "SELECT * FROM $wgdb.computername WHERE ComputerName LIKE '$name'";
	$result = mysql_query($query);
	while ($row = mysql_fetch_assoc($result)) {
		$mac = $row["MACAddress"];
		deleteMac($mac);
		mysql_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die(mysql_error());
		$count++;
	}
	return($count);
}

function queryComputer($name) {
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$name'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["id"]);
}

function queryComputerID($eth0,$eth1,$name) {
	include "./conf.php";
	$result = queryComputer($name);
	if ($result) return $result;
}

function queryName($mac) {
	include "./conf.php";
	$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$mac' OR WiMAC LIKE '$mac'";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	if ($row) return($row['ComputerName']);
	$query = "SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	if ($row) return($row['ComputerName']);
}

function addComputer($eth0,$eth1,$name) {
	include "./conf.php";
	$name = trim($name);
	$name = substr($name,0,15);
	//add macs to radius
	CleanComputerName($eth0);
	addMac($eth0);
	CleanComputerName($eth1);
	addMac($eth1);
	$id = queryComputerID($eth0,$eth1,$name); 
	if ($id == "") {
		$updated = $true
		$result = mysql_query("SELECT MAX(id) FROM $wgdb.computers") or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		$id = $row['MAX(id)']+1;
	}
	mysql_query("REPLACE INTO $wgdb.computers VALUES('$eth0','$eth1','$name',$id)") or die(mysql_error());
	if ($updated) return("$name Updated. "); else return("$name Added.  ")
}

function deleteComputer($target) {					//could be mac address or computer name
	include "./conf.php";
	@mysql_select_db($wgdb) or die(mysql_error());
	if (validateMac($target) == "" ) { //mac address
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$target' OR WiMAC LIKE '$target'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		if ($row == "") {
			deleteMac($target);
			return("$target removed.  ");
		}else $target = $row["ComputerName"];
	}
	if (queryComputer($target)) {
		//remove from raddb
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$target'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		deleteMac($row['ETHMAC']);
		deleteMac($row['WiMAC']);
		$query = "DELETE FROM $wgdb.computers WHERE ComputerName LIKE '$target'";
		mysql_query($query) or die(mysql_error());
		return("$target removed.  ");
	}
}
?>
