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
	$result = mysqli_query("SELECT * FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die("$query - " . mysqli_error());
	$row = mysqli_fetch_assoc($result);
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
	$result = mysqli_query("SELECT MAX(id) FROM $radb.radcheck") or die("$query - " . mysqli_error());
	$row = mysqli_fetch_assoc($result);
	$id = $row['MAX(id)']+1;
	mysqli_query("INSERT INTO $radb.radcheck VALUES('$id','$mac','Password','==','$mac')") or die("$query - " . mysqli_error());
	return("$mac Added to radcheck.  ");
}

function addMacGP($mac,$gp) {
	include "./conf.php";
	mysqli_query("INSERT INTO $radb.radreply VALUES(null,'$mac','filter-id',':=','$gp')") or die("$query - " . mysqli_error());
	return("Mac based Group Policy Updated for $mac.   ");
}

function deleteMac($mac) {
	include "./conf.php";
	@mysqli_select_db($radb) or die("Unable to select database");
	mysqli_query("DELETE FROM $radb.radcheck WHERE UserName LIKE '$mac'") or die("$query - " . mysqli_error());
	return("$mac deleted from radcheck.  ");
}

function deleteMacGP($mac){
	include "./conf.php";
	@mysqli_select_db($radb) or die("Unable to select database");
	mysqli_query("DELETE FROM $radb.radreply WHERE UserName LIKE '$mac'") or die("$query - " . mysqli_error());
	return("Mac based Group Policy Deleted for $mac.   ");
}

function queryComputerName($mac) {
	include "./conf.php";
	$result = mysqli_query("SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die("$query - " . mysqli_error());
	$row = mysqli_fetch_assoc($result);
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
		mysqli_query("REPLACE INTO $wgdb.computername VALUES('$mac','$name')") or die("$query - " . mysqli_error());
		return("Legacy $name added.  ");
	} else {
		mysqli_query("REPLACE INTO $wgdb.computername VALUES('$mac','$name')") or die("$query - " . mysqli_error());
		return("Legacy $name updated.  ");
	}
}

function deleteComputerName($mac) {
	include "./conf.php";
	$result = validateMac($mac);
	if ($result != "") return($result);	
	//remove from raddb
	deleteMac($mac);
	mysqli_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die("$query - " . mysqli_error());
	if (mysqli_affected_rows() == 1) return("$mac deleted.  ");
	else return("ERROR: $mac in ComputerNames db");
}

//returns # of rows deleted
function cleanComputerName($name) {
	include "./conf.php";
	$count = 0;
	$query = "SELECT * FROM $wgdb.computername WHERE ComputerName LIKE '$name'";
	$result = mysqli_query($query);
	while ($row = mysqli_fetch_assoc($result)) {
		$mac = $row["MACAddress"];
		deleteMac($mac);
		mysqli_query("DELETE FROM $wgdb.computername WHERE MACAddress LIKE '$mac'") or die("$query - " . mysqli_error());
		$count++;
	}
	return($count);
}

function queryComputer($name) {
	if($name=="") return;
	include "./conf.php";
	$result = mysqli_query("SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$name'") or die("$query - " . mysqli_error());
	$row = mysqli_fetch_assoc($result);
	if ($row == "") {
		return("");
	}else return($row["id"]);
}

function queryName($mac,$legacy=true) {
	if($mac=="") return;
	include "./conf.php";
	$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$mac' OR WiMAC LIKE '$mac'";
	$result = mysqli_query($query);
	$row = mysqli_fetch_assoc($result);
	if ($row) return($row['ComputerName']);
	if($legacy) {
	  $query = "SELECT * FROM $wgdb.computername WHERE MACAddress LIKE '$mac'";
	  $result = mysqli_query($query);
	  $row = mysqli_fetch_assoc($result);
		if ($row) return($row['ComputerName']);
	}
}

function querySN($sn) {
	if($sn=="") return;
	include "./conf.php";
	$query = "SELECT * FROM $wgdb.computers WHERE sn LIKE '$sn'";
	$result = mysqli_query($query);
	$row = mysqli_fetch_assoc($result);
	//if ($row) return("$row['ETHMAC'],$row['WiMAC'],$row['ComputerName'],$row['sn'],$row['id']");
	if ($row) $a =array("ETHMAC" => $row['ETHMAC'],"WiMAC"=>$row['WiMAC'],"ComputerName" => $row['ComputerName'],"sn" => $row['sn'],"id" => $row['id']);
	chdir("DeployStudio");
	include_once "DSFunctions.php";
	return(DSCatPlist(DSArrayToPlist($a)));
	chdir("../");
}

function searchComputers($text){
	include "./conf.php";
	$result = validateMac($text);
	if ($result) { //search text is not mac address
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '%$text%' OR sn LIKE '%$text%' ORDER BY ComputerName";
		$result = mysqli_query($query) or die("$query -" . mysqli_error());
		return(mysqli_fetch_object($result));
	} else {
		//computers table
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC Like '$text' OR WiMAC like '$text' ORDER BY ComputerName";
		$result = mysqli_query($query) or die(mysqli_error());
		return(mysqli_fetch_object($result));
	}
	
	
}

function addComputer($eth0,$eth1,$name,$sn=null,$gp=null,$id=null) {
	include "./conf.php";
	$updated = false;
	if(empty($name)) return ("Name must be supplied.   ");
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
		//find matching tuple by mac address pair unless null
		if ($sn) {
			$query = "SELECT id FROM $wgdb.computers WHERE sn LIKE '$sn'";
			$result = mysqli_query($query);
			if (mysqli_num_rows($result) > 1) return("$name - $sn Multiple Serial Numbers Matched.  ");
			$row = mysqli_fetch_assoc($result);
			if ($row) $id = $row['id'];
		} elseif ($eth0 && $eth1) {
		  	$query = "SELECT id FROM $wgdb.computers WHERE ETHMAC LIKE '$eth0' AND WiMAC LIKE '$eth1'";
		  	$result = mysqli_query($query);
		  	$row = mysqli_fetch_assoc($result);
		  	if ($row) $id = $row['id'];
		}
	}
	if ($id) {
		$updated=true;
		$query="SELECT * FROM $wgdb.computers WHERE id=$id";
		$result = mysqli_query($query) or die("$query - " . mysqli_error());
		$row = mysqli_fetch_assoc($result);
		//check if macs have changed, if so then delete from radcheck
		if(strcmp($row['ETHMAC'],$eth0) <> 0) deleteMac($row['ETHMAC']);
		if(strcmp($row['WiMAC'],$eth1) <> 0) deleteMac($row['WiMAC']);
		if(strcmp($row['filter-id'],$gp) <> 0) deleteMacGP($eth0);
		if(strcmp($row['filter-id'],$gp) <> 0) deleteMacGP($eth1);
		$query="REPLACE INTO $wgdb.computers VALUES('$eth0','$eth1','$name','$sn',$id,'$gp')";
		mysqli_query($query) or die("$query - " . mysqli_error());
		//Must delete from DS if sn is changed.  Only possible if already in computers.
		if (strcmp($row['sn'],$sn) <> 0) {
			chdir("DeployStudio");
			include_once "DSFunctions.php";
			DSDeleteComputer($name,$sn);
			chdir("../");
		}
	} else {
		//check for duplicate macs and names
		if ($name) $dup=queryComputer($name);
		if($dup) return("$name ERROR: Duplicate name.  ");
		if ($eth0) $dup=queryName($eth0,false);
		if($dup) return("$name ERROR: $dup has $eth0.  ");
		if ($eth1) $dup=queryName($eth1,false);
		if($dup) return("$name ERROR: $dup has $eth1.  ");
		$query="REPLACE INTO $wgdb.computers VALUES('$eth0','$eth1','$name','$sn',null,'$gp')";
		mysqli_query($query) or die("$query - " . mysqli_error());
	}
	//add macs to radius
	if ($eth0) {
		DeleteComputerName($eth0);
		addMac($eth0);
		if ($gp) addMacGP($eth0, $gp);
	}
  	if ($eth1) {
  		DeleteComputerName($eth1);
		addMac($eth1);
		if ($gp) addMacGP($eth1, $gp);
  	}
	if ($sn) {
		chdir("DeployStudio");
		include_once "DSFunctions.php";
		DSaddComputer($name,$sn);
		chdir("../");
	}
	if ($updated) return("$name Updated. "); else return("$name Added.  ");
}

function deleteComputer($eth0,$eth1,$ComputerName,$sn=NULL,$gp=NULL,$id=NULL) {
	include "./conf.php";
	@mysqli_select_db($wgdb) or die("$query - " . mysqli_error());
	if (!$id) {
		if ($sn) {
			$query = "SELECT * FROM $wgdb.computers WHERE sn LIKE '$sn'";
			$result = mysqli_query($query) or die("$query - " . mysqli_error());
			$row = mysqli_fetch_assoc($result);
			$id = $row['id'];
			if(!$id) return("$ComputerName with sn $sn doesn't exit");
		} elseif($eth0){
			$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$eth0'";
			$result = mysqli_query($query) or die("$query - " . mysqli_error());
			$row = mysqli_fetch_assoc($result);
			$id = $row['id'];
			if(!$id) return("$ComputerName doesn't exit");
		}
		elseif($eth1) {
			$query = "SELECT * FROM $wgdb.computers WHERE WiMAC LIKE '$eth1'";
			$result = mysqli_query($query) or die("$query - " . mysqli_error());
			$row = mysqli_fetch_assoc($result);
			$id = $row['id'];
			if(!$id) return("$ComputerName doesn't exit");
		} else return("No valid selection criteria for deleteComputer()");
	}
	$query = "DELETE FROM $wgdb.computers WHERE id=$id";
	mysqli_query($query) or die("$query - " . mysqli_error());
	if (mysqli_affected_rows() == 1) echo "$ComputerName deleted.  ";
	else return("ERROR: $ComputerName in Computers db");
	//remove from legacy database for completeness
	if (queryComputerName($eth0) != "") deleteComputerName($eth0);
	if (queryComputerName($eth1) != "") deleteComputerName($eth1);
	//remove from radcheck
	deleteMac($eth0);
	deleteMacGP($eth0);
	deleteMac($eth1);
	deleteMacGP($eth1);
	//remove from DeployStudio
	chdir("DeployStudio");
	include_once "DSFunctions.php";
	if ($sn) DSDeleteComputer($sn);
	chdir("../");
	return("$ComputerName removed.  ");

/*		
	if (validateMac($target) == "" ) { //mac address
		$query = "SELECT * FROM $wgdb.computers WHERE ETHMAC LIKE '$target' OR WiMAC LIKE '$target'";
		$result = mysqli_query($query) or die("$query - " . mysqli_error());
		$row = mysqli_fetch_assoc($result);
		if ($row == "") {
			deleteComputerName($target);
			return("$target removed.  ");
		}else $target = $row["ComputerName"];
	}
	if (queryComputer($target)) {
		//remove from raddb
		$query = "SELECT * FROM $wgdb.computers WHERE ComputerName LIKE '$target'";
		$result = mysqli_query($query) or die("$query - " . mysqli_error());
		$row = mysqli_fetch_assoc($result);
		deleteMac($row['ETHMAC']);
		deleteMac($row['WiMAC']);
		$query = "DELETE FROM $wgdb.computers WHERE ComputerName LIKE '$target'";
		mysqli_query($query) or die("$query - " . mysqli_error());
		chdir("DeployStudio");
		include "DSFunctions.php";
		DSDeleteComputer($target);
		return("$target removed.  ");
	}
	*/
}
?>
