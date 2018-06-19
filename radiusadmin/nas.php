<?php include "auth/checkLevel3.php"?>

<html>
<head>
<title>Manage Clients</title>
<link rel="stylesheet" href=./style.css>
</head>
<body>
<?php
include "./conf.php";
include "./nasFunctions.php";
#mysqli_connect(localhost,$user,$password);
#@mysqli_select_db($wgdb) or die("Unable to select database");

$rid = $_POST['rid'];
//$rclient=$_POST['rclient'];
if ($_POST['create']=='1'){
        /*echo "$_POST['rclient'],$_POST['rname'],$_POST['rpassword']";*/
	addClient($rid,$_POST['rclient'],$_POST['rname'],$_POST['rpassword']);
	$rid="";
}
if ($_POST['remove']=='1'){
	delClient($rid);
	$rid="";
}

#@mysqli_select_db($wgdb) or die("Unable to select database");
$query = "SELECT * FROM $radb.nas";
$result = mysqli_query($query) or die(mysqli_error());
//$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);

echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<input type=hidden name=remove value="0">
<input type=hidden name=add value="0">

EOM;
if (!$rid) echo "<input type=hidden name=rid value=\"\">";
else echo "<input type=hidden name=rid value=\"$rid\">";
echo <<<EOM
<br>
Manage Clients.<br>
Restart radiusd after any change.<br>
<table border=1>
<tr><th>IP</th><th>Name</th><th>Secret</th><th>action</th></tr>
EOM;
while($recordArray = mysqli_fetch_array($result,mysqli_ASSOC)) {
	if ($recordArray['id']==$rid) {
		printf("<tr bgcolor=red><td><input type=\"text\" name=\"rclient\" value=\"%s\"></td><td><input type=\"text\" name=\"rname\" value=\"%s\"></td><td><input type=\"password\" name=\"rpassword\"></td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='1'\"><input type=\"Submit\" class=\"button\" value=\"Remove\" OnClick=\"this.form.remove.value='1'\"></td></tr>\n",$recordArray['nasname'],$recordArray['shortname']);
	} else printf("<tr><td>%s</td><td>%s</td><td>****</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.rid.value='%s'\"></td></tr>\n",$recordArray['nasname'],$recordArray['shortname'],$recordArray['id']);
}
if (!$rid && $_POST['add']=='1') printf("<tr bgcolor=red><td><input type=\"text\" name=\"rclient\"></td><td><input type=\"text\" name=\"rname\"></td><td><input type=\"password\" name=\"rpassword\"></td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='1'\">\n");
if (!$rid && $_POST['add']!='1') echo "<tr><td></td><td></td><td></td><td><input type=\"Submit\" class=\"button\" value=\"Add\" OnClick=\"this.form.add.value='1'\"></td></tr>";
?>
</table>
<?php 
if ($rid || $_POST['add']) printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.rid.value=''\">");
?>
</form>
</body>
</html>