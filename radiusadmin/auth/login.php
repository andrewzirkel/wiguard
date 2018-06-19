<?php
session_start();

$errorMessage = "";
if (isset($_POST["user"]) && isset($_POST["pass"])) {
	require_once "./authFunctions.php";
	require_once "./authConf.php";
	
	$userName = $_POST["user"];
	$userPass = $_POST["pass"];
	
	#mysqli_connect(localhost,$user,$password);
#	#@mysqli_select_db($radb) or die("Unable to select database");
	$result = authUser($userName,$userPass);
	if ($result == 0) $errorMessage = "Login Failed.";
	else header("Location: ../reload.html");
}
?>
<html>
<head>
<title>Radius Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href=../style.css>
</head>

<body>

<?php
if ($errorMessage != '') {
echo "<p align='center'><strong><font color='#990000'> $errorMessage </font></strong></p>";
}
?>
<form action="" method="post" name="frmLogin" id="frmLogin">
 <table width="400" border="1" align="center" cellpadding="2" cellspacing="2">
  <tr>
   <td width="150">User Id</td>
   <td><input name="user" type="text" id="user"></td>
  </tr>
  <tr>
   <td width="150">Password</td>
   <td><input name="pass" type="password" id="pass"></td>
  </tr>
  <tr>
   <td width="150">&nbsp;</td>
   <td><input name="btnLogin" type="submit" id="btnLogin" value="Login"></td>
  </tr>
 </table>
</form>
</body>
</html>
