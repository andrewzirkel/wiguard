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
$url = DSFormatURL("server/get/info");
$a = DSGetData($url);
DSArrayToTable($a,false);
?>

</body>
</html>