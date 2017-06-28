<?php
header("Access-Control-Allow-Origin: *");
require('consts.php');
$ip = $_SERVER['REMOTE_ADDR'];
$skey = $_REQUEST['skey'];
$ssid = $server_hostname;
$uag = str_replace("&", " and ", $_SERVER['HTTP_USER_AGENT']);
$uag = str_replace(" ", "_", $uag);
$uag = str_replace(",", "-", $uag);
$ref = str_replace("&", " and ", $_SERVER['HTTP_REFERER']);
$dhash = sha1("$hash_salt$ip$skey$ssid$uag$ref");
$token = file_get_contents("https://$server_hostname/tokensvr.php?dhash=$dhash&ip=$ip&skey=$skey&ssid=$ssid&uag=$uag&ref=$ref");
die(json_encode(array("token"=>$token)));
?>
