<?php

require_once('../classes/CFPropertyList/CFPropertyList.php');

$ch = curl_init("http://greenwood.umasd.org:60080/server/get/info");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch,CURLOPT_USERPWD,'dsadmin:#anubis666');
$curlResult = curl_exec($ch);
curl_close($ch);

if ($curlResult == false) {
	echo "Curl Error";
	exit;
}

$plist = new CFPropertyList();
$plist->parse($curlResult);
var_dump($plist->toArray());

?>
