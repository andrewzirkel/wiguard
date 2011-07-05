<?php

//returns Admin User
function DSGetAdminUser() {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
	$result = mysql_query($query) or die(mysql_error());
	$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
	return($recordArray['DSAdminUser']);
}

//returns Admin Pass
function DSGetAdminPass() {
	include '../conf.php';
	$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
	$result = mysql_query($query) or die(mysql_error());
	$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
	return($recordArray['DSAdminPassowrd']);
}

function DSGetURL($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$creds = DSGetAdminUser() . DSGetAdminPass();
	curl_setopt($ch,CURLOPT_USERPWD,'$creds');
	$curlResult = curl_exec($ch);
	curl_close($ch);
	return $curlResult; 
}

function DSGetData($url) {
	//CFPropertyList works with Apple plists
	require_once('../classes/CFPropertyList/CFPropertyList.php');
	$result = DSGetURL($url);

	if (!$result) {
		echo "Connection to Deploy Studio Server at $url Failed";
		exit();
	}

	$plist = new CFPropertyList();
	$plist->parse($result);
	$a = $plist->toArray();
	return $a;
}

function DSArrayToTable($a,$sub) {
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
?>