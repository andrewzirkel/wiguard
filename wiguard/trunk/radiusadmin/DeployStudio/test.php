<?php
echo "<html><body><pre>";
require_once './DSFunctions.php';
$a = array("comptuername" => "azirkel-mbp","MAC" => "122345534");
$a = array("00:1b:63:9b:8c:44" => array("dstudio-auto-started-workflow" => "101130131422", "dstudio-hostname" => "azirkel-mbp","dstudio-mac-addr" => "00:1b:63:9b:8c:44"));
printf("%s",DSCatPlist(DSArrayToPlist($a)));
echo "</pre>";
echo "<br>workflows</br>";
printf("%s",DSArrayToTable(DSGetWorkflows()));
echo "</body></html>";

?>