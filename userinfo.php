<?php
require_once 'userctrl.php';

$user = $_REQUEST['user'];

if (!auth($user,$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"用户名/密码错误")));
}

if (!authAdmin($user,$_REQUEST['pswd'])) {
	// Also log login signature
	$token = $_REQUEST['token'];
	$skey = $_REQUEST['skey'];
	if (!$token || !$skey) {
		die(json_encode(array("status"=>"failure","reason"=>"验证失败")));
	}
	$logindb = file_get_contents("logins.log");
	$recur_login = 0;
	foreach (explode("\n", $logindb) as $line) {
		$uinfo = explode("\t", $line);
		if ($uinfo[1]==$user && $uinfo[2]==$token && $uinfo[3]==$skey) {
			$recur_login = 1;
		}
	}
	if ($recur_login==0) {
		file_put_contents("logins.log", $logindb.date('dS \of F Y h:i:s A')."\t$user\t$token\t$skey\n");
	}
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL Connect (userinfo.php): ".$con->connect_error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"无法连接至数据库.(错误:".$errid.")")));
}

$sql = "SELECT balance_stock, balance_futures, balance_currency, data FROM Users WHERE name='$user'";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL did not return (userinfo.php): ".$con->error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"数据库未返回.(错误:".$errid.")")));
}
$row = $result->fetch_assoc();
$udata = json_decode($row['data'], true);
$log = null;
if (array_key_exists("log", $udata)) {
	$log = $udata['log'];
	$log = str_replace("STOCK BONUS", "股票分红", $log);
	$log = str_replace("Unit", "股", $log);
	$log = str_replace("LIQUIDATE", "期货平仓", $log);
}
die(json_encode(array("status"=>"success","platform"=>file_get_contents("admin/tradestatus"),"balance_stock"=>$row["balance_stock"],"balance_futures"=>$row["balance_futures"],"balance_currency"=>$row["balance_currency"],"log"=>$log)));
