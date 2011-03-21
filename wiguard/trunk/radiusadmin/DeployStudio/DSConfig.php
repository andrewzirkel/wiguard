<?php include "../auth/checkLevel3.php"?>

<?php
include "../conf.php";

if ($_POST['update']=='1'){
	$v1=$_POST['DSServerURL'];
	$v2=$_POST['DSAdminUser'];
	$v3=$_POST['DSAdminPassword'];
	$query = "REPLACE INTO $wgdb.DSConfig VALUES('1','$v1','$v2','$v3')";
  mysql_query($query) or die(mysql_error());
}

$query = "SELECT * FROM $wgdb.DSConfig where ID=1";
$result = mysql_query($query) or die(mysql_error());
?>

<html>
<head>
<title>Delpoy Studio Configuration</title>
<link rel="stylesheet" href="../style.css">
</head>
<body style="font-family:Courier">
<center>Deploy Studio Configuration</center>
<form method="post">
<input type=hidden name=update value="0">
<input type=hidden name=modify value="0">
<br>
<table border=1 align="center">
<tr><th>URL</th><th>Admin User Name</th><th>Admin Password</th><th>action</th></tr>
<?php
$recordArray = mysql_fetch_array($result,MYSQL_ASSOC);
if ($_POST['modify']) printf("<tr bgcolor=red><td><input type=\"text\" name=\"DSServerURL\" value=\"%s\"></td><td><input type=\"text\" name=\"DSAdminUser\" value=\"%s\"></td><td><input type=\"password\" name=\"DSAdminPassword\"></td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.update.value='1'\"></td></tr>\n",$recordArray['DSServerURL'],$recordArray['DSAdminUser']);
	else printf("<tr><td>%s</td><td>%s</td><td>****</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.modify.value=1\"></td></tr>\n",$recordArray['DSServerURL'],$recordArray['DSAdminUser']);
echo "</table>";
if ($_POST['modify']) printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.modify.value=''\">");
?>
</form>
</body>
</html>