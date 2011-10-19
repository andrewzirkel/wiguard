<?php
chdir("/var/www/radiusadmin");
include "./functions.php";
addComputer('0011aabbccdd','0011aabbccdd',"sample");
include "./nasFunctions.php";
addClient(null,"172.17.0.0/16", "sampelSubnet", "test");