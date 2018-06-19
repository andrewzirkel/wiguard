<?php
function addClient($rid,$rclient, $rname, $rpassword) {
	include "./conf.php";
  //echo "$rclient, $rname, $rpassword";
	if (!$rid) $query = "REPLACE INTO $radb.nas SET nasname='$rclient',shortname='$rname',type='other',secret='$rpassword',description='RADIUS Client'";
	if ($rpassword) $query = "REPLACE INTO $radb.nas SET id='$rid',nasname='$rclient',shortname='$rname',type='other',secret='$rpassword',description='RADIUS Client'";
	/*
  if (!$rid) $query = "REPLACE INTO $radb.nas VALUES(NULL,'$rclient','$rname','other',NULL, '$rpassword',NULL,'RADIUS Client')";     //allow new db entries
	if ($rpassword) $query = "REPLACE INTO $radb.nas VALUES('$rid','$rclient','$rname','other',NULL, '$rpassword',NULL,'RADIUS Client')";
	*/
	else $query = "UPDATE $radb.nas SET shortname='$rname' WHERE nasname='$rclient'";
        mysqli_query($query) or die(mysqli_error());
}

function delClient($rid) {
	include "./conf.php";
	mysqli_query("DELETE FROM $radb.nas WHERE id LIKE '$rid'") or die(mysqli_error());
	$rows = mysqli_affected_rows();
    if ($rows == 1) return("$rid deleted from database"); else return("$rid not deleted from database");
}
?>
