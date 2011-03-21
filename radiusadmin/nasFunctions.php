<?php
function addClient($rid,$rclient, $rname, $rpassword) {
	include "./conf.php";
        //echo "$rclient, $rname, $rpassword";
        if (!$rid) $query = "REPLACE INTO $radb.nas VALUES(NULL,'$rclient','$rname','other',NULL, '$rpassword',NULL,'RADIUS Client')";     //allow new db entries
	if ($rpassword) $query = "REPLACE INTO $radb.nas VALUES('$rid','$rclient','$rname','other',NULL, '$rpassword',NULL,'RADIUS Client')";
	else $query = "UPDATE $radb.nas SET shortname='$rname' WHERE nasname='$rclient'";
        mysql_query($query) or die(mysql_error());
}

function delClient($rid) {
	include "./conf.php";
	mysql_query("DELETE FROM $radb.nas WHERE id LIKE '$rid'") or die(mysql_error());
	$rows = mysql_affected_rows();
    if ($rows == 1) return("$rid deleted from database"); else return("$rid not deleted from database");
}
?>
