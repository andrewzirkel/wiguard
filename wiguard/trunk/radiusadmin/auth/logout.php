<?php
session_start();

require_once("authFunctions.php");
logout();
if (isset($_get['page'])) {
	$page = $_get['page'];
	header("Location: $page");
}else header("Location: ../reload.html");

?>