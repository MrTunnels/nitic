<?php
require_once 'userctrl.php';
require_once 'serverlogs.php';

$user = $_REQUEST['user'];

if (!auth($user,$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"用户名/密码错误")));
}

$is_admin = authAdmin($user,$_REQUEST['pswd']);

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL Connect (stockinfo.php): ".$con->connect_error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"无法连接至数据库 (stockinfo.php).(错误:".$errid.")")));
}
$con->set_charset("utf8");

// Get user's holdings
$sql = "SELECT data FROM Users WHERE name='$user'";
$result = $con->query($sql);
$row = $result->fetch_assoc();
$userdata = json_decode($row['data'],true);

// Get stocks data
$sql = "SELECT * FROM Stocks";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL未返回 (stockinfo.php)")));
}

$data = array();

while($row = $result->fetch_assoc()){
	$name = $row['name'];
	$amt = 0;
	$orig_price = 0;

	foreach ($userdata['stocks'] as $stock) {
		if (strcmp($stock['name'],$name)==0) {
			$amt = $stock['amt'];
			$orig_price = $stock['price0'];
		}
	}

	if ($is_admin) {
		array_push($data, array("name"=>$row['name'],"leverage"=>$row['leverage'],"type"=>$row['type'],"description"=>$row['description'],"price"=>$row['price'],"data"=>json_decode($row['data']),"amt"=>$amt, "price0"=>$orig_price, "stats"=>$row['stats']));
	} else {
		array_push($data, array("name"=>$row['name'],"leverage"=>$row['leverage'],"type"=>$row['type'],"description"=>$row['description'],"price"=>$row['price'],"data"=>json_decode($row['data']),"amt"=>$amt, "price0"=>$orig_price));
	}
}

die(json_encode(array("status"=>"success","data"=>$data)));
