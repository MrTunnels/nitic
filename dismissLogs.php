<?php
require_once 'userctrl.php';
require_once 'serverlogs.php';
$user = $_REQUEST['user'];

if (!auth($user,$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"用户名/密码错误")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL Connect (dismissLogs.php): ".$con->connect_error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"无法连接至数据库.(错误:".$errid.")")));
}

$sql = "SELECT data FROM Users WHERE name='$user'";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL did not return (dismissLogs.php): ".$con->error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"数据库未返回.(错误:".$errid.")")));
}
$row = $result->fetch_assoc();
$udata = json_decode($row['data'], true);
unset($udata['log']);

$sql = "UPDATE Users SET data='".json_encode($udata)."' WHERE name='$user'";
if (!$con->query($sql)) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL update failed (dismissLogs.php): ".$con->error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"数据库更新请求失败.(错误:".$errid.")")));
}

die(json_encode(array("status"=>"success")));
