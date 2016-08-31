<?php
header("Access-Control-Allow-Origin: *");
$ip = $_SERVER['REMOTE_ADDR'];
$skey = $_REQUEST['skey'];
$ssid = "nfls.ga";
$uag = str_replace("&", " and ", $_SERVER['HTTP_USER_AGENT']);
$uag = str_replace(" ", "_", $uag);
$uag = str_replace(",", "-", $uag);
$ref = str_replace("&", " and ", $_SERVER['HTTP_REFERER']);
$dhash = sha1("3VblZ6z05ujElhUoehC4r8EsPppM4VM$ip$skey$ssid$uag$ref");
$token = file_get_contents("http://nitic.oscs.io/tokensvr.php?dhash=$dhash&ip=$ip&skey=$skey&ssid=$ssid&uag=$uag&ref=$ref");
die(json_encode(array("token"=>$token)));
?>
