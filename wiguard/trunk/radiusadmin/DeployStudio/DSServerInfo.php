<?php include "../auth/checkLevel1.php";
include "../conf.php";
require_once('./DSFunctions.php');
?>

<html>
<head>
<title>Deploy Studio Server Info</title>
<link rel="stylesheet" href=../style.css>
</head>
<body style="font-family:Courier">
<center>Deploy Studio Server Info</center>

<?php
$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
$result = mysql_query($query) or die(mysql_error());
$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);

$url = $recordArray['DSServerURL'] . "server/get/info";
$a = DSGetData($url);
DSArrayToTable($a,$false);
?>

</body>
</html>