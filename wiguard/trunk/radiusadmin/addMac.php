<?php include "auth/checkLevel2.php"?>

<html>
<head>
<title>Add MAC to radius</title>
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
		switch (count($elementArray)) {
			case 1:
				printf("%s<br>\n",addMac(trim($elementArray[0])));
				break;
			case 2:
				printf("%s<br>\n",addComputerName($elementArray[0],trim($elementArray[1])));
				break;
			case 3:
				printf("%s<br>\n",addComputer($elementArray[0],$elementArray[1],trim($elementArray[2])));
				break;
			default:
				echo ("Invalid Row: $line");
		}
	}
}

echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<br>
Add or update Mac addresses and computer names.<br>
Format: 0011aabbccdd,0011aabbccdd,ComputerName 
<br><textarea name="macList" rows="12" cols="50" style="font-family:Courier">$macList</textarea><br>
<input type="Submit" class="button" value="Add These MACs" OnClick="this.form.create.value=1">
</form>
EOM;
?>
</body>
</html>
