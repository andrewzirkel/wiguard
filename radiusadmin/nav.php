<?php session_start(); 
//require_once("auth/authFunctions.php")
?>
<html>
<head>
<title>Navigation Bar</title>
<link rel="stylesheet" href=style.css>
</head>
<body>
<br>
<?php
if (!isset($_SESSION["level"])) {
	echo ('<a href=auth/login.php target="right">Login</a><br>');
} else {
	$level = $_SESSION["level"];
	$user = $_SESSION["user"];
	if ($_SESSION["level"] > 2) {
		?>
		<a href=auth/users.php target="right">Manage Users</a><br>
                <a href=nas.php target="right">Manage Clients</a><br>
		<?php 
	}
	if ($_SESSION["level"] > 1) {
		?>
		<a href=addMac.php target="right">Add MAC</a><br>
		<a href=delete.php target="right">Delete MACs</a><br>
		<?php 
	}
	if ($_SESSION["level"] > 0) {?>
		<a href=list.php target="right">Show MACs</a><br>
		<a href=viewLog.php target="right">View Log</a><br>
    <a href="serverStatus.php" target="right">Server Status</a><br>
		<a href=welcome.php target="right">Home</a><br>
		<a href=auth/logout.php>Logout</a><br>
		<br>
		<hr>
	<?php
	}
	if ($_SESSION["level"] == 3) {?>
	<H4>Deploy Studio Integration</H4>
		<a href=DeployStudio/DSConfig.php target="right">Config</a><br>
		<a href=DeployStudio/DSServerInfo.php target="right">Server Status</a><br>
		<hr>
	<?php
	}
		echo "UserName = $user<br>";
		echo "Level = $level\n";
} ?>

</body>
</html>
