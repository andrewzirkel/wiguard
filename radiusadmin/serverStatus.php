<?php include "./conf.php"; ?>

<?php
function checkProcess($proc){
	return(shell_exec("ps -A | grep $proc"));
}
?>

<html>
<head>
<link rel="stylesheet" href=style.css>
<meta http-equiv="refresh" content="10">
</head>
<body>
<center><h1>Server Status</h1></center><br><br>
<?php
/*
<meter value="88" min="0" max="100">88</meter>
<progress value="1534602" max="4603807">33%</progress>
*/
$output = shell_exec("mpstat -P ALL");
echo "<pre>$output</pre>";
echo "<hr>";

echo "<H3>Service Status</H3>";

/*
if ($_POST['serviceaction']==1) {
    $service = $_POST['service'];
    echo "$service";
    $output = shell_exec("/etc/init.d/$service restart");
    echo "<pre>$output</pre>";
}
*/

echo <<<EOM
<form method="post">
<input type=hidden name=serviceaction value="0">
EOM;
echo "<table>";
foreach ($procs as $proc) {
	$output = checkProcess($proc);
	if ($output) {
		//echo "<tr><td>on</td><td>$proc</td><td><input type=\"hidden\" name=\"service\" value=\"$proc\"><input type=\"Submit\" class=\"button\" value=\"restart\" OnClick=\"this.form.serviceaction.value='1'\"></td></tr>";
            echo "<tr><td>on</td><td>$proc</td></tr>";
	} else {
		echo "<tr><td>off</td><td>$proc</td></tr>";
	}
}
echo "</table>";
echo "</form>";
echo "<hr>";

$output = shell_exec("free");
echo "<pre>$output</pre>";

$output = shell_exec("df -h");
echo "<pre>$output</pre>";
?>
</body>
</html>