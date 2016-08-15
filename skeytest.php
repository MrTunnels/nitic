<?php
header("Content-Type: application/json");
$db = file_get_contents("logins.log");
$skey = $_REQUEST['skey'];
foreach (explode("\n",$db) as $line) {
	$parts = explode("\t", $line);
	if (strcasecmp($parts[1], "test")==0) {
		if (strcasecmp($skey, $parts[3])==0) {
			die(json_encode(array("result"=>"FAIL")));
		}
	}
}

die(json_encode(array("result"=>"OK")));