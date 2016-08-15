<?php

require_once "userctrl.php";

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission denied.")));
}

function createUser($name, $pswd, $perm, $user_initfunds) {
	global $db_server, $db_user, $db_pswd, $db_name;
	$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
	if ($con->connect_error) {
		return "MySQL connect error (admin/createuser.php): ".$con->connect_error;
	}
	$init_data = json_encode(array("stocks"=>array(),"futures"=>array()));
	$sql = "INSERT INTO Users (name,pswd,type,balance_stock,balance_futures,data)
	VALUES('$name','$pswd','$perm',$user_initfunds ,$user_initfunds ,'$init_data')";
	if (!$con->query($sql)) {
		return "MySQL Error (admin/createuser.php): ".$con->error;
	}
	return "success";
}

$result = createUser($_REQUEST['reg_user'],$_REQUEST['reg_pswd'],$_REQUEST['reg_perm'],$_REQUEST['reg_funds']);
if (strcmp($result, "success")==0) {
	die(json_encode(array("status"=>"success")));
} else {
	die(json_encode(array("status"=>"failure","reason"=>$result)));
}
