<?php
session_start();

if ( !(isset($_SESSION['level']) && ($_SESSION['level'] >= 0))) {
	echo <<<EOM
<html>
<head>
<title>Authentication Error</title>
<link rel="stylesheet" href=style.css>
</head>
<body align=center><H1>User level access is needed for this page</H1></body>
</html>
EOM;
	exit();
}
?>
