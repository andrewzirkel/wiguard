<?php

require_once '../classes/CFPropertyList/CFPropertyList.php';

use CFPropertyList\CFPropertyList;
use CFPropertyList\CFDictionary;
use CFPropertyList\CFString;

$data=file_get_contents('/Users/zirkelad/BigProjects/WiGuard/CFPropertyListTesting/C17HT9YWDTY3-orig.plist');

function setValue($plist,$searchKey,$newValue){
	foreach( $plist->getValue(true) as $key => $value )
	{
		echo $key . PHP_EOL;
		if( $key == "dstudio-clientmanagement-computer-groups" )
		{
			echo print_r($value) . PHP_EOL;
		} elseif ( $value instanceof \Iterator ) {
			setValue($value,$key,$newValue);
		}
	}
}

function getValue($plist,$searchKey) {
	foreach( $plist->getValue(true) as $key => $value )
	{
		echo $key . PHP_EOL;
		if( $key == $searchKey )
		{
			echo print_r($value) . PHP_EOL;
		} elseif ( $value instanceof \Iterator ) {
			getValue($value,$searchKey);
		}
	}
}

$a=array('element1','element2');
print_r($a);
$plist = new CFPropertyList();
$plist->parse($data);
//getValue($plist,"dstudio-clientmanagement-computer-groups");
$a = $plist->toArray();
echo "<pre>" . "in DSGetData" . print_r($a) . "</pre>";
