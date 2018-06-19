<?php

function authUser($user, $passwd) {
	include "./authConf.php";
	$query = "SELECT * FROM $wgdb.$authTable WHERE user = '$user' AND password = PASSWORD('$passwd')";
	//echo "$query <br>";
	$result = mysqli_query($query) or die(mysqli_error());
	if (mysqli_num_rows($result) == 1) {
		//the user and pass match
		$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);
		$_SESSION['user'] = $user;
		$_SESSION['level'] = $recordArray['level'];
		return ($_SESSION['level']);
	} else return 0;
}

function addUser($ruser, $rpassword, $rlevel) {
	include "./authConf.php";
	if ($rpassword) mysqli_query("REPLACE INTO $wgdb.$authTable VALUES('$ruser',PASSWORD('$rpassword'),'$rlevel')") or die(mysqli_error());
	else {
		$query = "UPDATE auth SET level=$rlevel WHERE user='$ruser'"; 
		mysqli_query($query) or die(mysqli_error());
		if (mysqli_affected_rows() != 1) die("Database consistency error\n");
/*		$query = "SELECT * FROM $authTable WHERE user = '$ruser'";
    	$result = mysqli_query($query) or die(mysqli_error());
    	if (mysqli_num_rows($result) == 1) {
    		$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);
    		$rpassword = $recordArray['password'];
    	}
    	else die("Database consistency error\n");
    	echo "addUser full: REPLACE INTO $authTable VALUES('$ruser','$rpassword','$rlevel')";
    	mysqli_query("REPLACE INTO $authTable VALUES('$ruser','$rpassword','$rlevel')") or die(mysqli_error());
*/
	}
	/*
	else mysqli_query("REPLACE INTO $authTable (user,level) VALUES('$ruser','$rlevel')") or die(mysqli_error());
    if (!$rpassword) {
    	$query = "SELECT 'password' FROM $authTable WHERE user = '$ruser'";
    	$result = mysqli_query($query) or die(mysqli_error());
    	if (mysqli_num_rows($result) == 1) {
    		$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);
    		$rpassword = $recordArray['password'];
    	}
    	else die("Database consistency error\n");
    }
	mysqli_query("REPLACE INTO $authTable VALUES('$ruser',PASSWORD('$rpassword'),'$rlevel')") or die(mysqli_error());
	$rows = mysqli_affected_rows();
    if ($rows == 1) return("$rname Added to user database"); else return("$rname Updated in user database");
	*/
}

function delUser($ruser) {
	include "./authConf.php";
	mysqli_query("DELETE FROM $wgdb.$authTable WHERE user LIKE '$ruser'") or die(mysqli_error());
	$rows = mysqli_affected_rows();
    if ($rows == 1) return("$rname deleted from database"); else return("$rname not deleted from database");
}

/*Depricated
function getUsers() {
	require_once "./authConf.php";
	$query = "SELECT * FROM $authTable";
	//echo $query;
	$result = mysqli_query($query) or die(mysqli_error());
	//echo "exiting getUsers";
	return ($result);
}

function makeUserForm() {
	require_once "./authConf.php";
	$result=getUsers();
	$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);
	echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<input type=hidden name=delete value="0">
<input type=hidden name=edit value="0">
<input type=hidden name=user value="">
<br>
Manage Users.<br>
<table>
EOM;
	while($recordArray = mysqli_fetch_array($result,mysqli_ASSOC)) {
		if ($recordArray['user']==this.form.user.value) {
			printf("<tr bgcolor=red><td>%s</td><td>%s</td></tr>",$recordArray['user'],$recordArray['level']);
		}
		printf("<tr><td>%s</td><td>%s</td></tr>",$recordArray['user'],$recordArray['level']);
	}
	echo <<<EOM
</table>
</form>
EOM;
}
*/

function logOut() {
	if (isset($_SESSION['level'])) unset($_SESSION['level']);
}
?>