<?php
echo "<html><body><pre>";
require_once './DSFunctions.php';
/*
$a = array("comptuername" => "azirkel-mbp","MAC" => "122345534");
$a = array("dstudio-auto-started-workflow" => "101130131422", "dstudio-hostname" => "azirkel-mbp","dstudio-mac-addr" => "00:1b:63:9b:8c:44");
//printf("%s",DSCatPlist(DSArrayToPlist($a)));
$url = DSFormatURL("computers/set/entry?id=00:1b:63:9b:8c:44");
echo "$url\n";
$result = DSWriteData($url,DSCatPlist(DSArrayToPlist($a)));
//echo DSCatPlist(DSArrayToPlist($a));
echo "$result\n";
echo "</pre>";
echo "<br>workflows</br>";
printf("%s",DSArrayToTable(DSGetWorkflows()));
*/
printf("%s",DSArrayToTable(DSGetComputers()));
echo "</body></html>";
?>