<?php
use CFPropertyList\CFPropertyList;
use CFPropertyList\CFDictionary;
use CFPropertyList\CFString;
use CFPropertyList\CFTypeDetector;
//global variables:
$DSComputersCache=null;

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
	return $url;
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
	$groupname="";
	if (sizeof($a) > 1) $groupname = strtolower($a[0]);
	elseif (! empty($groupDefault)) return $groupname = $groupDefault;
	
	if (empty($groupname)) return FALSE;
	else {
		settype($groupname, 'string');
		return $groupname;
	}
}
//returns retrieved data
//takes url
function DSGetURL($url) {
	include '../conf.php';
	$url = DSNormalizeURl($url);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	$creds = DSGetAdminUser() . ":" . DSGetAdminPass();
	curl_setopt($ch,CURLOPT_USERPWD,"$creds");
	$curlResult = curl_exec($ch);
	if($debug) echo "<pre>In DSGetURL\n" . $url. PHP_EOL . $curlResult . "\n</pre>";
	$r=curl_getinfo($ch);
	if ($r['http_code']!=200) exit("DS returned error code: " . $r['http_code']);
	curl_close($ch);
	return $curlResult; 
}

//write data back to server
//takes url and data (plain text plist)
//returns error code
function DSWriteData($url,$data=null) {
	include '../conf.php';
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
	if($debug) echo "<pre>In DSWriteData\n" . $url. PHP_EOL . $data . PHP_EOL . $curlResult . "\n</pre>";
	$r=curl_getinfo($ch);
	if ($r['http_code']!=201) exit("DS returned error code: " . $r['http_code']);
	curl_close($ch);
	return $curlResult; 
}

//returns array from plist at $url
function DSGetData($url) {
	include '../conf.php';
	//CFPropertyList works with Apple plists
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$result = DSGetURL($url);

	if (!$result) {
		//echo "Connection to Deploy Studio Server at $url Failed";
		return(null);
	}

	$plist = new CFPropertyList();
	$plist->parse($result);
	$a = $plist->toArray();
	
	if($debug) printf("<pre>In DSGetData\n%s\n%s\n</pre>",$url,print_r($a,TRUE));
	return $a;
}

function DSArrayToTable($a,$sub=false) {
	echo "<table>\n";
	foreach ($a as $key => $element) {
		if (is_array($element)) {
			echo "<tr><td collspan=2>" . $key;
			echo "<ul>\n";
			DSArrayToTable($element,true);
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
function DSConstructPlist($a,&$plist) {
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$td = new CFTypeDetector();
	$guessedStructure = $td->toCFType( $a );
	$plist->add( $guessedStructure );
}
/*
function DSConstructPlist($a,&$plist,$dictKey=null,&$dictP=null) {
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$dict = new CFDictionary();
	foreach ($a as $key => $element) {
	  if (is_array($element)) DSConstructPlist($element,$plist,$key,$dict);
	  else $dict->add($key,new CFString($element));
	}
	if ($dictKey) $dictP->add($dictKey,$dict);
	else $plist->add($dict);
}
*/

//set value to key in plist object
//takes plist object, key, value
//returns 
function DSSetValue($plist,$mykey, $myvalue) {
	foreach( $plist->getValue(true) as $key => $value )
	{
		if( $key == $mykey ) $value->setValue( $myvalue );
		if( $value instanceof \Iterator ) DSSetValue($plist, $mykey, $myvalue);
	}
	
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
function DSGetComputers($refresh=FALSE) {
	global $DSComputersCache;
	if($refresh || (!isset($DSComputersCache))) {
		$url = DSFormatURL("computers/get/all");
		$DSComputersCache = DSGetData($url);
	}
	return($DSComputersCache);
}

//return array of all DS Groups
function DSGetGroups($refresh=FALSE) {
	global $DSGroupsCache;
	if($refresh || (!isset($DSGroupsCache))) {
		$url=DSFormatURL("computers/groups/get/all");
		$DSGroupsCache = DSGetData($url);
	}
	return($DSGroupsCache);
}

function DSGetComputerEntry($sn){
	$computers=DSGetComputers();
	if (isset($computers['computers'][$sn])) return $computers['computers'][$sn];
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
		if (array_key_exists('group',$element)) $query = $query . ",DSWorkflows.group=\"" . $element['group'] . "\"";
		mysql_query($query) or die("$query - " . mysql_error());
	}
}

//Set Group attributes
function DSSetGroupAttributes($DSGroup){
	
}

function DSSetWorkflow($id,$DSWorkflow,$updateDS=true) {
	include '../conf.php';
	if(strcmp($DSWorkflow,"none")==0) $DSWorkflow = null;
	if ($DSWorkflow) $query = "UPDATE $wgdb.DSGroups SET DSWorkflow='$DSWorkflow' WHERE id='$id'";
	else $query = "UPDATE $wgdb.DSGroups SET DSWorkflow='null' WHERE id='$id'";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	if ($updateDS) {
	echo <<<EOM
<!-- Progress bar holder -->
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information -->
<div id="information" style="width"></div>
EOM;
		$query = "SELECT * FROM $wgdb.DSGroups WHERE id=$id";
		$result = mysql_query($query) or die("$query - " . mysql_error());
		$row = mysql_fetch_assoc($result);
		$url=DSFormatURL("computers/groups/set/entry")."?id=".$row['DSGroup'];
		//get current group data
		$computers=DSGetComputers();
		if (! is_array($computers['groups'])) return;
		if (! is_array($computers['groups'][$row['DSGroup']])) return;
		$data=$computers['groups'][$row['DSGroup']];
		//set workflow for group
		if($DSWorkflow) $data['dstudio-auto-started-workflow']="$DSWorkflow";
		//unset workflow if null
		elseif(array_key_exists('dstudio-auto-started-workflow',$data)) unset($data['dstudio-auto-started-workflow']);
		//echo "<pre>" . DSCatPlist(DSArrayToPlist($data)) . "</pre>";
		DSWriteData($url,DSCatPlist(DSArrayToPlist($data)));
		//set computer data (...because this is something the client would handle...)
		$pbtotal=count($computers['computers']);
		$pbcount=0;
		foreach ($computers['computers'] as $key => $element) {
			$pbcount++;
			if (strcmp($element['dstudio-group'],$row['DSGroup']) == 0) {
				$pbpercent=intval($pbcount/$pbtotal * 100)."%";
				$pbinfo="Processing " . $element['dstudio-hostname'];
				echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$pbpercent.';background-image:url(../assets/pbar-ani.gif);\">&nbsp;</div>";
    document.getElementById("information").innerHTML="'.$pbinfo.'";
    </script>';
				$url=DSFormatURL("computers/set/entry")."?id=".$key;
				//set workflow
				$element["dstudio-auto-started-workflow"] = "$DSWorkflow";
				//unset if no workflow
				if(!$DSWorkflow) unset($element["dstudio-auto-started-workflow"]);
				DSWriteData($url,DSCatPlist(DSArrayToPlist($element)));
				echo str_repeat(' ',1024*64);
				// Send output to browser immediately
				flush();
			}
		}
		// Tell user that the process is completed
		echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
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
	$newURL=DSFormatURL("computers/groups/new/entry");
	$renURL=DSFormatURL("computers/groups/ren/entry");
	$group = trim($group);
	if (!is_array(DSQueryGroup($group))) { //group doens't exist
		$query = "INSERT INTO $wgdb.DSGroups SET id=null,DSGroup='$group'";
		mysql_query($query) or die("$query - " . mysql_error());
		$created=true;
	}

	//The DS Admin app creates a new group and then renames it
	$currentGroups=DSGetGroups(FALSE);
	if (is_array($currentGroups['groups'])) if (in_array($group,$currentGroups['groups'])) return($created);
	DSWriteData($newURL);
	$currentGroups2=DSGetGroups(TRUE);
	//get new group name that was created 
	$newGroup=array_merge(array_diff($currentGroups2['groups'],$currentGroups['groups']));  //array_diff only unsets
	if ($newGroup==NULL) exit("DSError, could not create group");
	$url=$renURL."?id=".$newGroup[0]."&new_id=".$group;
	DSWriteData($url);
	//refresh Groups cache
	DSGetGroups(TRUE);
	//refresh computers to pick up new group settings
	DSGetComputers(TRUE);
	return($created);
}

//returns True if groups create, false if not.
//do we need this...groups are created when a mchine is added
//we do to clear stale groups...maybe break that out?
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
		if (!in_array($currentGroup['DSGroup'],$groups)) {
			$query = "DELETE FROM $wgdb.DSGroups where id=$currentGroup[id]";
			mysql_query($query) or die("$query - " . mysql_error());
		}
	}
	return($created);
}

function DSClearStaleGroups() {
	include '../conf.php';
	$computers=DSGetComputers();
	if (! is_array($computers['groups'])) return;
	$query = "SELECT * FROM $wgdb.DSGroups";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	while ($currentGroup = mysql_fetch_assoc($result)) {
		if (!array_key_exists($currentGroup['DSGroup'],$computers['groups'])) {
			$query = "DELETE FROM $wgdb.DSGroups where id=$currentGroup[id]";
			mysql_query($query) or die("$query - " . mysql_error());
		}
	}
}

//Sync group settings from DS (default workflow)
function DSSyncGroupData() {
	$computers=DSGetComputers();
	if (! is_array($computers['groups'])) return;
	foreach ($computers['groups'] as $key => $element) {
		$groupTuple = DSQueryGroup($key);
		if ($groupTuple) {
			if (array_key_exists('dstudio-auto-started-workflow',$element)) DSSetWorkflow($groupTuple['id'],$element['dstudio-auto-started-workflow'],false);
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
	if (! is_array($computers['groups'])) return(FALSE);
	if (! is_array($computers['groups']["$DSGroup"])) return(FALSE);
	$data=array_remove_empty($computers['groups']["$DSGroup"]);
	foreach ($data as $key => $element) {
		if (substr_count($key,"dstudio-group") > 0) unset($data[$key]);
	}
	return($data);
}

//removes existing group data
//returns array of data without group data
function DSRemoveExistingGroupData($existingData) {
	if (! array_key_exists('dstudio-group', $existingData)) return $existingData;
	$groupSettings = DSGetGroupSettings($existingData['dstudio-group']);
	return array_diff($existingData, $groupSettings);
}

/*
function DSAddComputer($name,$mac) {
	include '../conf.php';
	if($debug) echo "In DSAddComputers";
	if (! (DSIntegration())) return;
	$mac=DSFormatMac($mac);
	//construct plist
	$plist = array("dstudio-host-primary-key" => "dstudio-mac-addr","dstudio-hostname" => "$name","dstudio-mac-addr" => "$mac");
	//determine group
	$computerGroup = DSParseGroup($name);
	if ($computerGroup) {
			if(! DSAddGroup($computerGroup)){ //false if group not created, so must grab group data
				$groupSettings = DSGetGroupSettings($computerGroup);
				if ($groupSettings) $plist = array_merge($plist,$groupSettings);
				if($debug) echo "$computerGroup exists but no data found";
			}
	} else return; 		//not adding manchines without group data!
	//write plist to DS Database
	if ("$computerGroup") $plist["dstudio-group"] = $computerGroup;
	$url = DSFormatURL("computers/set/entry?id=$mac");
	if($debug) echo $url;
	if($debug) echo "<pre>" . DSCatPlist(DSArrayToPlist($plist)) . "<pre>";
	DSWriteData($url,DSCatPlist(DSArrayToPlist($plist)));
}
*/

//deploy studio now keys off of serial number
function DSAddComputer($name,$sn) {
	include '../conf.php';
	if($debug) echo "In DSAddComputers\n";
	if (! (DSIntegration())) return;
	//construct plist
	$plist = array(
			"dstudio-host-primary-key" => "dstudio-host-serial-number",
			"dstudio-hostname" => "$name",
			"dstudio-host-serial-number" => "$sn",
	);
	//determine group
	$computerGroup = DSParseGroup($name);
	if ($computerGroup) {	
		if(! DSAddGroup($computerGroup)){
			//false if group not created, so must grab group data
			$groupSettings = DSGetGroupSettings($computerGroup);
			if ($groupSettings!==FALSE) $plist = array_merge($plist,$groupSettings);
			else echo "Computer Group: $computerGroup exists but no data found\n";
		}
	} else return; 		//not adding machines without group data!
	//write plist to DS Database
	if ("$computerGroup") $plist["dstudio-group"] = $computerGroup;
	//get existing data for host
	$existingData=DSGetComputerEntry($sn);
	if(! empty($existingData)) {
		//remove existing group data from host
		$existingData = DSRemoveExistingGroupData($existingData);
		$plist = array_merge($existingData,$plist);
	}
	//only write data if we have something changed.
	if((empty($existingData)) || is_array(array_diff($plist,$existingData))) {
		$url = DSFormatURL("computers/set/entry?id=$sn");
		DSWriteData($url,DSCatPlist(DSArrayToPlist($plist)));
	}
}

/*
function DSDeleteComputer($mac) {
	include '../conf.php';
	if (! (DSIntegration())) return;
	$mac=DSFormatMac($mac);
	$url = DSFormatURL("computers/del/entry?id=$mac");
	DSWriteData($url,NULL);
}
*/

function DSDeleteComputer($sn) {
	include '../conf.php';
	if (! (DSIntegration())) return;
	//$mac=DSFormatMac($mac);
	$url = DSFormatURL("computers/del/entry?id=$sn");
	DSWriteData($url,NULL);
}

//Master sync function, ensures all wiguard data is in DS
/*
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
*/

function DSSync() {
	include '../conf.php';
	//Sync Computers
	echo <<<EOM
<!-- Progress bar holder -->
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information -->
<div id="information" style="width"></div>
EOM;
	$query = "SELECT * FROM $wgdb.computers";
	$result = mysql_query($query) or die("$query - " . mysql_error());
	$pbtotal=mysql_num_rows($result);
	$pbcount=0;
	//Need to do this to set up initial DS Database, otherwise can't craete groups.
	DSGetComputers();
	while($computer = mysql_fetch_assoc($result)) {
		$pbcount++;
		$pbpercent=intval($pbcount/$pbtotal * 100)."%";
		//construct array
		if($computer['sn']=="") continue;
		//$mac = DSFormatMac($computer['ETHMAC']);
		//DSComputers = DSGetComputers();
		//if (! array_key_exists($mac,$DSComputers)) continue;	//computer already defined--does this matter?
		$pbinfo=$computer['ComputerName'];
		echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$pbpercent.';background-image:url(../assets/pbar-ani.gif);\">&nbsp;</div>";
    document.getElementById("information").innerHTML="'.$pbinfo.'";
    </script>';
		DSAddComputer($computer['ComputerName'],$computer['sn']);
		// This is for the buffer achieve the minimum size in order to flush data
		echo str_repeat(' ',1024*64);
		// Send output to browser immediately
		flush();
	}
	// Tell user that the process is completed
	echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
}

?>