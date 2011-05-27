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

function queryComputer($mac) {
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["ComputerName"]);
}

function queryComputerName($name) {
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$name'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["ComputerName"]);
}

function addMac($mac) {
	include "./conf.php";
	#what id to use:
	$result = mysql_query("SELECT MAX(id) FROM $radb.radcheck") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$id = $row['MAX(id)']+1;
	mysql_query("INSERT INTO $radb.radcheck VALUES('$id','$mac','Password','==','$mac')") or die(mysql_error());
	return("$mac Added to radcheck.  ");
}

function addComputer($eth0,$eth1,$name) {
	include "./conf.php";
	$name = trim($name);
	$name = substr($name,0,15);
	if (queryComputer($eth0)) cleanComputerName($eth0);							//check for and remove name from old table "computername"
	if (queryComputer($eth1)) cleanComputerName($eth1);
	mysql_query("REPLACE INTO $wgdb.computers VALUES('$eth0','$eth1','$name')") or die(mysql_error());
	$rows = mysql_affected_rows();
	if ($rows == 1) return("$name Added to computername  "); else return("$name Updated in computername");
}

function cleanComputerName($mac) {
	include "./conf.php";
	mysql_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die(mysql_error());
}

function deleteMac($mac) {
	include "./conf.php";
	@mysql_select_db($radb) or die("Unable to select database");
	mysql_query("DELETE FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die(mysql_error());
	$message = "$mac deleted from radcheck";
	@mysql_select_db($wgdb) or die("Unable to select database");
	mysql_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die(mysql_error());
	return("$message $mac deleted from computername.  ");
}
?>
