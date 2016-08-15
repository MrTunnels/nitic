<?php
require_once "consts.php";

function getUserID() {
	return intVal(file_get_contents("latest_user_id"));
}

function createUser($pswd) {
if (strlen($pswd)==0) {
return "请勿刷新页面或尝试绕过表达验证.";
}
	global $db_server, $db_user, $db_pswd, $db_name;
	$name = getUserID()+1;
	if ($name>35) {
//		return "MySQL Error: Cannot connect to database. Check log for more info";
	}
	file_put_contents("latest_user_id", "$name");
	$name = "NITIC2015-".$name;
	$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
	if ($con->connect_error) {
		return "MySQL connect error (admin/createuser.php): ".$con->connect_error;
	}
	$init_data = json_encode(array("stocks"=>array(),"futures"=>array()));
	$sql = "INSERT INTO Users (name,pswd,type,balance,data)
	VALUES('$name','$pswd','user',2000000,'$init_data')";
	if (!$con->query($sql)) {
		return "MySQL Error (admin/createuser.php): ".$con->error;
	}
	return "success";
}

$result = createUser($_REQUEST['pswd']);
if (strcmp($result, "success")==0) {
	die(json_encode(array("status"=>"success","userid"=>"NITIC2015-".getUserID())));
} else {
	die(json_encode(array("status"=>"failure","reason"=>$result)));
}
