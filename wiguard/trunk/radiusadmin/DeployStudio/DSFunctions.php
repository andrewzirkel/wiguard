<?php

//returns true if enabled, false if not
function DSIntegration() {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
	if ($recordArray['DSIntegrate']) return(true);
	else return (false);
}

//returns Admin User
function DSGetAdminUser() {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
	return($recordArray['DSAdminUser']);
}

//returns Admin Pass
function DSGetAdminPass() {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
	return($recordArray['DSAdminPassword']);
}

//returns full url
//take path to append
function DSFormatURL($path) {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
  $result = mysql_query($query) or die("$query - " . mysql_error());
  $recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
	return($recordArray['DSServerURL'] . "$path"); 
}

function DSNormalizeURL($url) {
	$url = str_replace(" ", "%20", $url);
	return($url);
}

//add ':' back into mac
//returns formatted mac
function DSFormatMac($mac) {
	return(substr($mac,0,2).':'.substr($mac,2,2).':'.substr($mac,4,2).':'.substr($mac,6,2).':'.substr($mac,8,2).':'.substr($mac,10,2));
}

//returns group portion of name based on $groupDelim
function DSParseGroup($name) {
	include '../conf.php';
	$a = explode($groupDelim,$name);
	if (sizeof($a) > 1) return(strtolower($a[0]));
	else return(false);
}
//returns retrieved data
//takes url
function DSGetURL($url) {
	$url = DSNormalizeURl($url);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	$creds = DSGetAdminUser() . ":" . DSGetAdminPass();
	curl_setopt($ch,CURLOPT_USERPWD,"$creds");
	$curlResult = curl_exec($ch);
	curl_close($ch);
	return $curlResult; 
}

//write data back to server
//takes url and data (plain text plist)
//returns error code
function DSWriteData($url,$data=null) {
	$url = DSNormalizeURl($url);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$creds = DSGetAdminUser() . ":" . DSGetAdminPass();
	curl_setopt($ch,CURLOPT_USERPWD,"$creds");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,"$data");
	$curlResult = curl_exec($ch);
	curl_close($ch);
	return $curlResult; 
}

//returns array from plist at $url
function DSGetData($url) {
	//CFPropertyList works with Apple plists
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$result = DSGetURL($url);

	if (!$result) {
		echo "Connection to Deploy Studio Server at $url Failed";
		return(1);
	}

	$plist = new CFPropertyList();
	$plist->parse($result);
	$a = $plist->toArray();
	return $a;
}

function DSArrayToTable($a,$sub=false) {
	echo "<table>\n";
	foreach ($a as $key => $element) {
		if (is_array($element)) {
			echo "<tr><td collspan=2>" . $key;
			echo "<ul>\n";
			DSArrayToTable($element,$true);
			echo "</ul></tr>\n";
		} else {
			echo "<tr><td>";
			if ($sub) echo "<li>";
			echo $key;
			if ($sub) echo "</li>";
			echo "</td><td>" . $element . "</td></tr>\n";
		}
	}
	echo "</table>\n";
}

//returns plist object
//takes associative [multi] array, plist opbject
function DSConstructPlist($a,&$plist,$dictKey=null,&$dictP=null) {
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$dict = new CFDictionary();
	foreach ($a as $key => $element) {
	  if (is_array($element)) DSConstructPlist($element,$plist,$key,$dict); else $dict->add($key,new CFString($element));
	}
	if ($dictKey) $dictP->add($dictKey,$dict); else $plist->add($dict);
}

//returns plist object
//takes associative [multi] array
function DSArrayToPlist($a) {
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$plist = new CFPropertyList();
	DSConstructPlist($a,$plist);
	return($plist);
}

//returns plist text
//takes plist object
function DSCatPlist($plist) {
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	return($plist->toXML(true));
}

//return array of all DS Computers
function DSGetComputers() {
	$url = DSFormatURL("computers/get/all");
	return(DSGetData($url));
}

//returns multi array of workflows
function DSGetWorkflows() {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSWorkflows";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	$a=array();
	while($row = mysql_fetch_assoc($result)) {
		$a[$row['ID']]["title"] = $row['title'];
		$a[$row['ID']]["group"] = $row['group'];
	}
	return($a);
}

//Sync workflow data from Deploy Studio
function DSSyncWorkflows() {
	include '../conf.php';
	$currentWorkflows = DSGetWorkflows();
	$url = DSFormatURL("workflows/get/all");
	$workflows = DSGetData($url);
	foreach ($currentWorkflows as $key => $element) {
		if (! array_key_exists($key,$workflows)) {
			$query = "DELETE FROM $wgdb.DSWorkflows WHERE ID=\"$key\"";
			mysql_query($query) or die("$query - " . mysql_error());
		}
	}
	foreach ($workflows as $key => $element) {
		$query = "REPLACE INTO $wgdb.DSWorkflows SET ID=\"$key\",description=\"" . $element['description'] . "\",title=\"" . $element['title'] . "\"";
		if ($element['group']) $query = $query . ",DSWorkflows.group=\"" . $element['group'] . "\"";
		mysql_query($query) or die("$query - " . mysql_error());
	}
}

//
function DSSetWorkflow($id,$DSWorkflow,$updateDS=true) {
	include '../conf.php';
	if(strcmp($DSWorkflow,"none")==0) $DSWorkflow = null;
	if ($DSWorkflow) $query = "UPDATE $wgdb.DSGroups SET DSWorkflow='$DSWorkflow' WHERE id='$id'";
	else $query = "UPDATE $wgdb.DSGroups SET DSWorkflow='null' WHERE id='$id'";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	if ($updateDS) {
		$query = "SELECT * FROM $wgdb.DSGroups WHERE id=$id";
		$result = mysql_query($query) or die("$query - " . mysql_error());
		$row = mysql_fetch_assoc($result);
		$url=DSFormatURL("computers/groups/set/entry")."?id=".$row['DSGroup'];
		//get current group data
		$computers=DSGetComputers();
		if (! is_array($computers['groups'])) break;
		if (! is_array($computers['groups'][$row['DSGroup']])) break;
		/*
		$data=array("CN" => "",
		"dstudio-auto-started-workflow" => "$DSWorkflow",
		"dstudio-auto-reset-workflow" => "NO",
		"dstudio-auto-disable" => "NO",
		"dstudio-bootcamp-windows-computer-name" => "",
		"dstudio-bootcamp-windows-product-key" => "",
		"dstudio-group-hosname-index-first-value" => 1,
		"dstudio-group-hosname-index-length" => 1,
		"dstudio-group-name" => $row['DSGroup'],
		"dstudio-host-delete-other-locations" => "NO",
		"dstudio-host-interfaces" => array ( "en0" => array (
		"dstudio-dns-ips" => "",
		"dstudio-host-airport" => "NO",
		"dstudio-host-airport-name"  => "",
		"dstudio-host-airport-password" => "",
		"dstudio-host-ftp-proxy" => "NO",
		"dstudio-host-ftp-proxy-port"  => "",
		"dstudio-host-ftp-proxy-server"  => "",
		"dstudio-host-http-proxy" => "NO",
		"dstudio-host-http-proxy-port" => "",
		"dstudio-host-http-proxy-server" => "",
		"dstudio-host-https-proxy" => "NO",
		"dstudio-host-https-proxy-port" => "",
		"dstudio-host-https-proxy-server" => "",
		"dstudio-host-interfaces" => "en0",
		"dstudio-host-ip" => "",
		"dstudio-router-ip" => "",
		"dstudio-search-domains" => "",
		"dstudio-subnet-mask" => "")),
		"dstudio-host-location" => "",
		"dstudio-host-new-network-location" => "NO",
		"dstudio-hostname" => "",
		"dstudio-serial-number" => "",
		"dstudio-xsan-license" => "");
		*/
		$data=$computers['groups'][$row['DSGroup']];
		$data['dstudio-auto-started-workflow']="$DSWorkflow";
		//echo "<pre>" . DSCatPlist(DSArrayToPlist($data)) . "</pre>";
		DSWriteData($url,DSCatPlist(DSArrayToPlist($data)));
		//set computer data (...because this is something the client would handle...)
		foreach ($computers['computers'] as $key => $element) {
			if (strcmp($element['dstudio-group'],$row['DSGroup']) == 0) {
				$url=DSFormatURL("computers/set/entry")."?id=".$key;
				$element["dstudio-auto-started-workflow"] = "$DSWorkflow";
				DSWriteData($url,DSCatPlist(DSArrayToPlist($element)));
			}
		}
	}
}

//returns array of group tuple, nothing if not found
//takes group name
function DSQueryGroup($group) {
	include '../conf.php';	
	$query = "SELECT * FROM $wgdb.DSGroups WHERE STRCMP(DSGroup,'$group') = 0"; //group is reserved word, must quote
	$result = mysql_query($query) or die("$query - " . mysql_error());
	$num_rows = mysql_num_rows($result);
	$row = mysql_fetch_assoc($result);
	switch ($num_rows) {
		case 0:
			return("");
			break;
		case 1:
			return($row);
			break;
		default:
			echo "ERROR: Multiple Rows returned by DSQueryGroup";
			return($row);
	}
}

//returns true if created
function DSAddGroup($group) {
	include '../conf.php';
	$created=false;
	$getURL=DSFormatURL("computers/groups/get/all");
	$newURL=DSFormatURL("computers/groups/new/entry");
	$renURL=DSFormatURL("computers/groups/ren/entry");
	$group = trim($group);
	if (!is_array(DSQueryGroup($group))) { //group doens't exist
		$query = "INSERT INTO $wgdb.DSGroups SET id=null,DSGroup='$group'";
		mysql_query($query) or die("$query - " . mysql_error());
		$created=true;
	}

	//The DS Admin app creates a new group and then renames it
	$currentGroups=DSGetData($getURL);
	if (is_array($currentGroups['groups'])) if (in_array($group,$currentGroups['groups'])) return($created);
	DSWriteData($newURL);
	$currentGroups2=DSGetData($getURL);
	$newGroup=array_merge(array_diff($currentGroups2['groups'],$currentGroups['groups']));  //array_diff only unsets
	$url=$renURL."?id=".$newGroup[0]."&new_id=".$group;
	DSWriteData($url);
	return($created);
}

//returns True if groups create, false if not.
//do we need this...groups are created when a mchine is added
function DSGenerateGroups() {
	include '../conf.php';
	$created = false;
	$groups=array();
	//get computer tuples	
	$query = "SELECT * FROM $wgdb.computers";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	while($computer = mysql_fetch_assoc($result)) {
		$group = DSParseGroup($computer['ComputerName']);
		if ($group) {
			if (!in_array($group,$groups)) $groups[] = $group;
			$created=DSAddGroup($group);
		}
	}
	//clear stale groups
	$query = "SELECT * FROM $wgdb.DSGroups";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	while ($currentGroup = mysql_fetch_assoc($result)) {
		if (!in_array($currentGroup[DSGroup],$groups)) {
			$query = "DELETE FROM $wgdb.DSGroups where id=$currentGroup[id]";
			mysql_query($query) or die("$query - " . mysql_error());
		}
	}
	return($created);
}

//Sync group settings from DS (default workflow)
function DSSyncGroupData() {
	$computers=DSGetComputers();
	if (! is_array($computers['groups'])) return;
	foreach ($computers['groups'] as $key => $element) {
		$groupTuple = DSQueryGroup($key);
		if ($groupTuple) {
			if ($element['dstudio-auto-started-workflow']) DSSetWorkflow($groupTuple['id'],$element['dstudio-auto-started-workflow'],false);
			else DSSetWorkflow($groupTuple['id'],null,false);
		}
	}
}

function array_remove_empty($arr){
    $narr = array();
    while(list($key, $val) = each($arr)){
        if (is_array($val)){
            $val = array_remove_empty($val);
            // does the result array contain anything?
            if (count($val)!=0){
                // yes :-)
                $narr[$key] = $val;
            }
        }
        else {
            if (trim($val) != ""){
                $narr[$key] = $val;
            }
        }
    }
    unset($arr);
    return $narr;
}

function DSGetGroupSettings($DSGroup) {
	$computers=DSGetComputers();
	if (! is_array($computers['groups'])) return(false);
	if (! is_array($computers['groups']["$DSGroup"])) return(false);
	$data=array_remove_empty($computers['groups']["$DSGroup"]);
	foreach ($data as $key => $element) {
		if (substr_count($key,"dstudio-group") > 0) unset($data[$key]);
	}
	/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	*/
	return($data);
}

function DSAddComputer($name,$mac) {
	include '../conf.php';
	if (! (DSIntegration())) return;
	$mac=DSFormatMac($mac);
	//construct plist
	$plist = array("dstudio-hostname" => "$name","dstudio-mac-addr" => "$mac");
	//determine group
	$computerGroup = DSParseGroup($name);
	if ($computerGroup) {
			if(! DSAddGroup($computerGroup)){ //false if group not created, so must grab group data
				$groupSettings = DSGetGroupSettings($computerGroup);
				if ($groupSettings) $plist = array_merge($plist,$groupSettings);
				//else echo "$computerGroup exists but no data found";
			}
	} else return; 		//not adding manchines without group data!
	//write plist to DS Database
	if ("$computerGroup") $plist["dstudio-group"] = $computerGroup;
	$url = DSFormatURL("computers/set/entry?id=$mac");
	//echo "<pre>" . DSCatPlist(DSArrayToPlist($plist)) . "<pre>";
	DSWriteData($url,DSCatPlist(DSArrayToPlist($plist)));
}

function DSDeleteComputer($mac) {
	include '../conf.php';
	if (! (DSIntegration())) return;
	$mac=DSFormatMac($mac);
	$url = DSFormatURL("computers/del/entry?id=$mac");
	DSWriteData($url,NULL);
}

//Master sync function, ensures all wiguard data is in DS
function DSSync() {
	include '../conf.php';
	//Sync Computers
	$query = "SELECT * FROM $wgdb.computers";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	while($computer = mysql_fetch_assoc($result)) {
		//construct array
		if($computer['ETHMAC']=="") continue;
		//$mac = DSFormatMac($computer['ETHMAC']);
		//DSComputers = DSGetComputers();
		//if (! array_key_exists($mac,$DSComputers)) continue;	//computer already defined--does this matter?
		DSAddComputer($computer['ComputerName'],$computer['ETHMAC']);
	}
}
?>