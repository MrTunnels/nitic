<?php

require_once 'userctrl.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied")));
}

if (!isset($_REQUEST['quote'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Please specify a quote")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL connect error (admin/update.php) :".$con->connect_error)));
}
$con->set_charset("utf8");

// Update stocks
$sql = "DELETE FROM Stocks WHERE name='$_REQUEST[quote]'";
$result = $con->query($sql);
if (!$result || $con->errno) {
	die(json_encode(array("status"=>"failure","reason"=>$con->error)));
}

die(json_encode(array("status"=>"success")));

