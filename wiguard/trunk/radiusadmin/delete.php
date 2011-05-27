<?php include "auth/checkLevel2.php"?>

<html>
<head>
<title>Delete MAC from radius</title>
<link rel="stylesheet" href=style.css>
</head>
<body>

<?php
include "./conf.php";
include "./functions.php";
$create=$_POST['create'];
if ($create == 1) {
	echo "<br>Modifying Database...<br>\n";
	$macList=$_POST['macList'];

	$lineArray = explode("\n",$macList);
	foreach ($lineArray as $line) {
		$elementArray = explode(",",$line);
		$elementArray[0] = trim($elementArray[0]);
		$result = validateMac($elementArray[0]);

	$macArray = explode("\n",$macList);
	foreach ($macArray as $mac) {
		$mac = trim($mac);
		$result = validateMac($mac);
		echo "<br>";
		if ($result == "") {
			$result = queryMac($mac);
			if ($result != "") {
				$result = deleteMac($mac);
				echo $result;
			}else echo $result;
		} else echo $result;
	}
}

echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<br>
MAC: <br><textarea name="macList" rows="12" cols="32" style="font-family:Courier">$macList</textarea><br>
<input type="Submit" class="button" value="Delete These MACs" OnClick="this.form.create.value=1">
</form>
EOM;
?>
</body>
</html>
