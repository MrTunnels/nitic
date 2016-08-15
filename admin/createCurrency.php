<?php

require_once 'userctrl.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL connect error (admin/CreateMoneyType.php) :".$con->connect_error)));
}
$con->set_charset("utf8");

$type = $_REQUEST['name'];
$description = $_REQUEST['description'];

$sql = "INSERT INTO MoneyType (Name,description)
VALUES ('$type','$description')";

$con->query($sql);

die(json_encode(array("status"=>"success")));
