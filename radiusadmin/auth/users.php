<?php include "checkLevel3.php"?>

<?php
function generateSelect($level) {
	$returnString="<select name=rlevel>\n";
	for($i=1;$i<=3;$i++) {
		if ($i==$level) $returnString=$returnString . "<option selected>$i</option>\n";
		else $returnString=$returnString . "\t<option>$i</option>\n";
	}
	$returnString=$returnString . "</select>\n";
	return ($returnString);
}
?>

<html>
<head>
<title>Manage Radius users</title>
<link rel="stylesheet" href=../style.css>
</head>
<body>
<?php
include "./authConf.php";
include "./authFunctions.php";
#mysqli_connect(localhost,$user,$password);
#@mysqli_select_db($wgdb) or die("Unable to select database");

$ruser=isset($_POST['ruser']) ? $_POST['ruser'] : "";
if (isset($_POST['create']) && $_POST['create']){
	addUser($ruser,$_POST['rpassword'],$_POST['rlevel']);
	$ruser="";
}
if (isset($_POST['remove']) && ($_POST['remove']=='1')){
	delUser($ruser);
	$ruser="";
}

#@mysqli_select_db($wgdb) or die("Unable to select database");
$query = "SELECT * FROM $wgdb.$authTable";
$result = $mysqli->query($query) or die(mysqli_error());
//$recordArray = mysqli_fetch_array($result,mysqli_ASSOC);

echo <<<EOM
<form method="post">
<input type=hidden name=create value="0">
<input type=hidden name=remove value="0">
<input type=hidden name=add value="0">
EOM;
if (!$ruser) echo "<input type=hidden name=ruser value=\"\">";
else echo "<input type=hidden name=ruser value=\"$ruser\">";
echo <<<EOM
<br>
Manage Users.<br><br>
<table border=1>
<tr><th>login</th><th>password</th><th>level</th><th>action</th></tr>
EOM;
while ($row = mysqli_fetch_assoc($result)) {
	if ($row['user']==$ruser) {
		printf("<tr bgcolor=red><td>%s</td><td><input type=\"password\" name=\"rpassword\"></td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='1'\"><input type=\"Submit\" class=\"button\" value=\"Remove\" OnClick=\"this.form.remove.value='1'\"></td></tr>\n",$row['user'],generateSelect($row['level']));
	} elseif (!in_array($row['user'],$specialUser)) printf("<tr><td>%s</td><td>****</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.ruser.value='%s'\"></td></tr>\n",$row['user'],$row['level'],$row['user']);
}
mysqli_free_result($result);
if (!$ruser && (isset($_POST['add']) && ($_POST['add']=='1'))) printf("<tr bgcolor=red><td><input type=\"text\" name=\"ruser\"></td><td><input type=\"password\" name=\"rpassword\"></td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.create.value='1'\">\n",generateSelect(0));
?>
<tr><td></td><td></td><td></td><td><input type="Submit" class="button" value="Add" OnClick="this.form.add.value='1'"></td></tr>
</table>
<?php
if ($ruser || (isset($_POST['add']) && $_POST['add'])) printf("<input type=\"Submit\" class=\"button\" value=\"Cancel\" OnClick=\"this.form.ruser.value=''\">")
?>
</form>
</body>
