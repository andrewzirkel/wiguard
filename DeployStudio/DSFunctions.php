<?php

function DSGetURL($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch,CURLOPT_USERPWD,'dsadmin:#anubis666');
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
?>