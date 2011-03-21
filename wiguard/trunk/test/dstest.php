<?php

$context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("dsadmin:#anubis666")
    )
));
$data = file_get_contents('http://greenwood.umasd.org:60080/computers/get/all',false,$context);
echo $data;
?>