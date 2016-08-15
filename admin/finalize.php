<?php
header("Content-Type: application/json");

$user = $_REQUEST['user'];
$pswd = $_REQUEST['pswd'];

require_once "userctrl.php";

if (!authAdmin($user,$pswd)){
  die("Permission Denied.");
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);

if ($con->connect_error) {
	$errid = generateID();
	errLog("[Error-".$errid."]","MySQL Connect (stockinfo.php): ".$con->connect_error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"无法连接至数据库 (stockinfo.php).(错误:".$errid.")")));
}

// Build database

$sql = "SELECT * FROM Stocks";
$result = $con->query($sql);
$stockdb = array();
while ($row = $result->fetch_assoc()) {
	$stockdb[$row['name']] = $row;
}
$bonus = json_decode($_REQUEST['bonus'],true);


// Read users

$sql = "SELECT * FROM Users";

$result = $con->query($sql);

while($row = $result->fetch_assoc()){
	$udata = json_decode($row['data'],true);
	$username = $row['name'];
	$log = "";
	$stk_bal = $row['balance_stock'];
	$fut_bal = $row['balance_futures'];
	foreach($udata['stocks'] as $key => $stk){
		if ($stockdb[$stk['name']]['type']==="STK") {
			// Stock
			$bonus_get = floatval($bonus[$stk['name']]) * $stk['amt'];
			$bonus_get = intval($bonus_get);
			$log .= "STOCK BONUS: ".$stk['name']."(".$bonus[$stk['name']]."/Unit) +".$bonus_get."<br>";
			$stk_bal += $bonus_get;
		} else if ($stockdb[$stk['name']]['type']==="FUT") {
			$amt = $udata['stocks'][$key]['amt'];
			if ($amt!=0) {
				$fut_get = abs($amt)*$udata['stocks'][$key]['price0']/$stockdb[$stk['name']]['leverage']+($amt)*($stockdb[$stk['name']]['price']-$udata['stocks'][$key]['price0']);
				$log .= "LIQUIDATE: ".$stk['name']." ";
				if ($fut_get > 0) {
					$log .= "+";
				}
				$fut_bal += $fut_get;
				$log .= "$fut_get<br>";
				$udata['stocks'][$key]['amt'] = 0;
			}
		}
	}
	// Commit user data to db
	if (strlen($log)>0) {
		$udata['log'] = $log;
	}
	$sql = "UPDATE Users SET balance_stock=$stk_bal, balance_futures=$fut_bal, data='".json_encode($udata)."' WHERE name='$username'";
	if (!$con->query($sql)) {
		die(json_encode(array('status'=>"failure", 'reason'=>"MySQL Error:".$con->error)));
	}
}

die(json_encode(array('status'=>"success")));

