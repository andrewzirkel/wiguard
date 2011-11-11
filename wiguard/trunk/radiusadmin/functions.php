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
	$result = mysql_query("SELECT * FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die("$query - " . mysql_error());
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
	$result = mysql_query("SELECT MAX(id) FROM $radb.radcheck") or die("$query - " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$id = $row['MAX(id)']+1;
	mysql_query("INSERT INTO $radb.radcheck VALUES('$id','$mac','Password','==','$mac')") or die("$query - " . mysql_error());
	return("$mac Added to radcheck.  ");
}

function deleteMac($mac) {
	include "./conf.php";
	@mysql_select_db($radb) or die("Unable to select database");
	mysql_query("DELETE FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die("$query - " . mysql_error());
	return("$mac deleted from radcheck.  ");
}

function queryComputerName($mac) {
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die("$query - " . mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["ComputerName"]);
}

function addComputerName($mac,$name) {
	include "./conf.php";
	if ($mac) {
		$mac = strtolower($mac);
		$result = validateMac($mac);
		if ($result) {
			return("$mac not formatted correctly, $name not added");
		}
	}
	if (queryComputerName($mac) == "") {
		addmac($mac);
		mysql_query("REPLACE INTO $wgdb.computername VALUES('$mac','$name')") or die("$query - " . mysql_error());
		return("Legacy $name added.  ");
	} else {
		mysql_query("REPLACE INTO $wgdb.computername VALUES('$mac','$name')") or die("$query - " . mysql_error());
		return("Legacy $name updated.  ");
	}
}

function deleteComputerName($mac) {
	include "./conf.php";
	$result = validateMac($mac);
	if ($result != "") return($result);	
	//remove from raddb
	deleteMac($mac);
	mysql_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die("$query - " . mysql_error());
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
		mysql_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die("$query - " . mysql_error());
		$count++;
	}
	return($count);
}

function queryComputer($name) {
	if($name=="") return;
	include "./conf.php";
	$result = mysql_query("SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$name'") or die("$query - " . mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["id"]);
}

function queryName($mac,$current=false) {
	if($mac=="") return;
	include "./conf.php";
	$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$mac' OR WiMAC LIKE '$mac'";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	if ($row) return($row['ComputerName']);
	if(!$current) {
	  $query = "SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'";
	  $result = mysql_query($query);
	  $row = mysql_fetch_assoc($result);
		if ($row) return($row['ComputerName']);
	}
}

function addComputer($eth0,$eth1,$name,$id=null) {
	include "./conf.php";
	$updated = true;
	$name = trim($name);
	$name = substr($name,0,15);
	//validate macs
	if ($eth0) {
		$eth0 = strtolower($eth0);
		$result = validateMac($eth0);
		if ($result) {
			return("$eth0 not formatted correctly, $name not added");
		}
	}
	if ($eth1) {
		$eth1 = strtolower($eth1);
		$result = validateMac($eth1);
		if ($result) {
			return("$eth1 not formatted correctly, $name not added");
		}
	}
	if (! $id) {
		//find matching tuple by mac address pair
		$query = "SELECT id FROM $wgdb.computers WHERE ETHMAC LIKE '$eth0' AND WiMAC LIKE '$eth1'";
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		if ($row == "") {
			$updated = false;
//	Shouldn't need this with auto increment
//			$result = mysql_query("SELECT MAX(id) FROM $wgdb.computers") or die("$query - " . mysql_error());
//			$row = mysql_fetch_assoc($result);
//			$id = $row['MAX(id)']+1;
		} else {
			$id = $row['id'];
		}
	} 
	if ($id) {
		$query="SELECT * FROM $wgdb.computers WHERE id=$id";
		$result = mysql_query($query) or die("$query - " . mysql_error());
		$row = mysql_fetch_assoc($result);
		//check if macs have changed, if so then delete from radcheck
		if(strcmp($row['ETHMAC'],$eth0) <> 0) deleteMac($row['ETHMAC']);
		if(strcmp($row['WiMAC'],$eth1) <> 0) deleteMac($row['WiMAC']);
		$query="REPLACE INTO $wgdb.computers VALUES('$eth0','$eth1','$name',$id)";
		mysql_query($query) or die("$query - " . mysql_error());
		//Must delete from DS if eth0 is changed.  Only possible if already in computers.
		if (strcmp($row['ETHMAC'],$eth0) <> 0) {
			chdir("DeployStudio");
			include_once "DSFunctions.php";
			DSDeleteComputer($row['ETHMAC']);
			chdir("../");
		}
	}
	else {
		//check for duplicate macs and names
		$dup=queryComputer($name);
		if($dup) return("$name ERROR: Duplicate name.  ");
		$dup=queryName($eth0,true);
		if($dup) return("$name ERROR: $dup has $eth0.  ");
		$dup=queryName($eth1,true);
		if($dup) return("$name ERROR: $dup has $eth1.  ");
		$query="REPLACE INTO $wgdb.computers VALUES('$eth0','$eth1','$name',null)";
		mysql_query($query) or die("$query - " . mysql_error());
	}
	//add macs to radius
	CleanComputerName($eth0);
	addMac($eth0);
	CleanComputerName($eth1);
	addMac($eth1);
	chdir("DeployStudio");
	include_once "DSFunctions.php";
	DSaddComputer($name,$eth0);
	chdir("../");
	if ($updated) return("$name Updated. "); else return("$name Added.  ");
}

function deleteComputer($eth0,$eth1,$ComputerName,$id=NULL) {
	include "./conf.php";
	@mysql_select_db($wgdb) or die("$query - " . mysql_error());
	if (!$id) {
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$eth0'";
		$result = mysql_query($query) or die("$query - " . mysql_error());
		$row = mysql_fetch_assoc($result);
		$id = $row['id'];
		if(!$id) return("$ComputerName doesn't exit");
	}
	$query = "DELETE FROM $wgdb.computers WHERE id=$id";
	mysql_query($query) or die("$query - " . mysql_error());
	if (mysql_affected_rows() == 1) echo "$ComputerName deleted.  ";
	else return("ERROR: $ComputerName in Computers db");
	//remove from legacy database for completeness
	if (queryComputerName($eth0) != "") deleteComuterName($eth0);
	if (queryComputerName($eth1) != "") deleteComuterName($eth1);
	//remove from radcheck
	deleteMac($eth0);
	deleteMac($eth1);
	//remove from DeployStudio
	chdir("DeployStudio");
	include_once "DSFunctions.php";
	DSDeleteComputer($eth0);
	chdir("../");
	return("$ComputerName removed.  ");

/*		
	if (validateMac($target) == "" ) { //mac address
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$target' OR WiMAC LIKE '$target'";
		$result = mysql_query($query) or die("$query - " . mysql_error());
		$row = mysql_fetch_assoc($result);
		if ($row == "") {
			deleteComputerName($target);
			return("$target removed.  ");
		}else $target = $row["ComputerName"];
	}
	if (queryComputer($target)) {
		//remove from raddb
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$target'";
		$result = mysql_query($query) or die("$query - " . mysql_error());
		$row = mysql_fetch_assoc($result);
		deleteMac($row['ETHMAC']);
		deleteMac($row['WiMAC']);
		$query = "DELETE FROM $wgdb.computers WHERE ComputerName LIKE '$target'";
		mysql_query($query) or die("$query - " . mysql_error());
		chdir("DeployStudio");
		include "DSFunctions.php";
		DSDeleteComputer($target);
		return("$target removed.  ");
	}
	*/
}
?>
