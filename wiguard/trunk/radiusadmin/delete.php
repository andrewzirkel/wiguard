<?php include "auth/checkLevel2.php"?>

<html>
<head>
<title>Delete Computers from WiGuard</title>
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
				printf("%s<br>\n",deleteMac(trim($elementArray[0])));
				break;
			case 2:
				printf("%s<br>\n",deleteComputerName(trim($elementArray[0])));
				break;
			case 3:
				printf("%s<br>\n",deleteComputer(trim($elementArray[0]),trim($elementArray[1]),trim($elementArray[2])));
				break;
		case 4:
			 printf("%s<br>\n",deleteComputer(trim($elementArray[0]),trim($elementArray[1]),trim($elementArray[2]),trim($elementArray[3])));
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
Computer Record: 
<br><textarea name="macList" rows="12" cols="50" style="font-family:Courier">$macList</textarea><br>
<input type="Submit" class="button" value="Delete These MACs" OnClick="this.form.create.value=1">
</form>
EOM;
?>
</body>
</html>
