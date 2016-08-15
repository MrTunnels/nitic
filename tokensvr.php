<?php
if (array_key_exists("dhash", $_REQUEST)) {
	header("Content-Type: text/plain");
	$ip = $_REQUEST['ip'];
	$skey = $_REQUEST['skey'];
	$ssid = $_REQUEST['ssid'];
	$uag = $_REQUEST['uag'];
	$ref = $_REQUEST['ref'];
	$dhash = sha1("3VblZ6z05ujElhUoehC4r8EsPppM4VM$ip$skey$ssid$uag$ref");
	if ($_REQUEST['dhash']===$dhash) {
		$token = sha1("nitic-".date("dS \of F Y h:i:s ").rand());
		file_put_contents("tokens.csv", file_get_contents("tokens.csv")."$token, $ip, $ssid, $skey, $uag, $ref\n");
		die($token);
	} else {
		file_put_contents("tokensvr_badreq.log", file_get_contents("tokensvr_badreq.log")."$ip/$skey@$ssid($uag) REQUESTFROM:".$_SERVER['REMOTE_ADDR']."(".$_SERVER['HTTP_USER_AGENT'].") at page ".$_SERVER['HTTP_REFERER']." \n");
		die();
	}
} else {
	header("Content-Type: application/json");
	die(json_encode(array("http://app.starrystudio.org")));
}
