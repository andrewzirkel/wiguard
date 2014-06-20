<?php include "auth/checkLevel2.php"?>

<html>
<head>
<title>Delete Computers from WiGuard</title>
<link rel="stylesheet" href=style.css>
</head>
<body>
<!-- Progress bar holder -->
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information -->
<div id="information" style="width"></div>
<?php
include "./conf.php";
include "./functions.php";
$create=$_POST['create'];
if ($create == 1) {
	echo "<br>Modifying Database...<br>\n";
	$macList=$_POST['macList'];
	$lineArray = explode("\n",$macList);
	$pbtotal=sizeof($lineArray);
	$pbcount=0;
	foreach ($lineArray as $line) {
		$pbcount++;
		$pbpercent=intval($pbcount/$pbtotal * 100)."%";
		$pbinfo="";
		$elementArray = explode(",",$line);
		switch (count($elementArray)) {
			case 1:
				$pbinfo=deleteMac(trim($elementArray[0]));
				printf("%s<br>\n",$pbinfo);
				break;
			case 2:
				$pbinfo=deleteComputerName(trim($elementArray[0]));
				printf("%s<br>\n",$pbinfo);
				break;
			case 3:
				$pbinfo=deleteComputer(trim($elementArray[0]),trim($elementArray[1]),trim($elementArray[2]));
				printf("%s<br>\n",$pbinfo);
				break;
			case 4:
				$pbinfo=deleteComputer(trim($elementArray[0]),trim($elementArray[1]),trim($elementArray[2]),trim($elementArray[3]));
			 	printf("%s<br>\n",$pbinfo);
			 	break;
   			default:
   				$pbinfo="Invalid Row: $line";
				printf("%s<br>\n",$pbinfo);
		}
		// Javascript for updating the progress bar and information
		echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$pbpercent.';background-image:url(assets/pbar-ani.gif);\">&nbsp;</div>";
    document.getElementById("information").innerHTML="'.$pbinfo.'";
    </script>';
		// This is for the buffer achieve the minimum size in order to flush data
		echo str_repeat(' ',1024*64);
		// Send output to browser immediately
		flush();
	}
	// Tell user that the process is completed
	echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
	echo '<form method="post">
	<input type=hidden name=create value="0">
	<br>
	<input type="Submit" class="button" value="Continue" OnClick="this.form.create.value=0">
	</form>';
}else{
echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<br>
Computer Record: 
<br><textarea name="macList" rows="12" cols="50" style="font-family:Courier">$macList</textarea><br>
<input type="Submit" class="button" value="Delete These MACs" OnClick="this.form.create.value=1">
</form>
EOM;
}
?>
</body>
</html>
