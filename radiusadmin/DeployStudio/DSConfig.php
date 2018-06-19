<?php include "../auth/checkLevel3.php"?>
<html>
<head>
<title>Delpoy Studio Configuration</title>
<link rel="stylesheet" href="../style.css">
</head>
<body style="font-family:Courier">
<center>Deploy Studio Configuration</center>
<?php
include "../conf.php";
include "./DSFunctions.php";

if (isset($_POST['modify']) && $_POST['modify']=='1') {
	$modify = true;
}else{
	$modify = false;
}

if (isset($_POST['update']) && $_POST['update']=='1'){
	$v1=$_POST['DSServerURL'];
	$v2=$_POST['DSAdminUser'];
	$v3=$_POST['DSAdminPassword'];
	if (isset($_POST['DSIntegrate']) && $_POST['DSIntegrate']) $v4=1; else $v4 = 0;
	$query = "REPLACE INTO $wgdb.DSConfig SET ID='1',DSServerURL='$v1',DSAdminUser='$v2',DSAdminPassword='$v3',DSIntegrate='$v4'";
  mysqli_query($query) or die(mysqli_error());
  if (isset($_POST['DSIntegrate']) && $_POST['DSIntegrate']) {
  	echo "Synchronizing to DeployStudio...";
  	DSSync();
  }
}

$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
$result = mysqli_query($query) or die(mysqli_error());
?>

<form method="post">
<input type=hidden name=update value="0">
<input type=hidden name=modify value="0">
<br>
<table border=1 align="center">
<tr><th>URL</th><th>Admin User Name</th><th>Admin Password</th><th>Enable Integration</th><th>action</th></tr>
<?php
$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);
if ($recordArray['DSIntegrate']) {
	if ($modify) $integrateCheckbox = "<input type=checkbox name=\"DSIntegrate\" value=1 checked>";
	else $integrateCheckbox = "<input type=checkbox name=\"DSIntegrate\" value=1 disabled checked>";
} else {
	if ($modify) $integrateCheckbox = "<input type=checkbox name=\"DSIntegrate\" value=1>";
	else $integrateCheckbox = "<input type=checkbox name=\"DSIntegrate\" value=1 disabled>";	
}
if ($modify) printf("<tr bgcolor=red><td><input type=\"text\" name=\"DSServerURL\" value=\"%s\"></td><td><input type=\"text\" name=\"DSAdminUser\" value=\"%s\"></td><td><input type=\"password\" name=\"DSAdminPassword\"></td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.update.value='1'\"></td></tr>\n",$recordArray['DSServerURL'],$recordArray['DSAdminUser'],$integrateCheckbox);
	else printf("<tr><td>%s</td><td>%s</td><td>****</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.modify.value=1\"></td></tr>\n",$recordArray['DSServerURL'],$recordArray['DSAdminUser'],$integrateCheckbox);
echo "</table>";
if ($modify) printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.modify.value=''\">");
?>
</form>
</body>
</html>