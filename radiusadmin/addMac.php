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
		$elementArray[0] = trim($elementArray[0]);
		$result = validateMac($elementArray[0]);
		echo "<br>";
		if ($result == "") {
			$elementArray[1] = trim($elementArray[1]);
			$result = validateMac($elementArray[1]);
			if ($result == "") {												//second entry is wireless mac address
				if ($elementArray[2] != "") {
					//add macs to radius
					$result = queryMac($elementArray[0]);
					if ($result == "") {
						$result = addMac($elementArray[0]);
						echo $result;
					} else echo $result;
					$result = queryMac($elementArray[1]);
					if ($result == "") {
						$result = addMac($elementArray[1]);
						echo $result;
					} else echo $result;
					#@mysql_select_db($wgdb) or die("Unable to select database");
					//add to computers
					$result = addComputer($elementArray[0],$elementArray[1],$elementArray[2]);
					echo "$elementArray[0],$elementArray[1] associated with $result";
				}else echo "No computer name Supplied, NOT ADDED";
			} else {
				$result = queryMac($elementArray[0]);
				if ($result == "") {
					$result = addMac($elementArray[0]);
					echo $result;
				} else echo $result;
				$computerName = $elementArray[1];
				if ($computerName != "") {
					#@mysql_select_db($wgdb) or die("Unable to select database");
					$result = addComputer($elementArray[0],NULL,$computerName);
					echo "$elementArray[0] associated with $result";
				}else echo "No computer name Supplied";
			}
		} else echo $result;
	}
}

echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<br>
Add or update Mac addresses and computer names.<br>
Format: 0011aabbccdd,0011aabbccdd,ComputerName 
<br><textarea name="macList" rows="12" cols="32" style="font-family:Courier">$macList</textarea><br>
<input type="Submit" class="button" value="Add These MACs" OnClick="this.form.create.value=1">
</form>
EOM;
?>
</body>
</html>
