<?php
require_once 'userctrl.php';
require_once 'serverlogs.php';

if (strcmp(file_get_contents("admin/tradestatus"),"open")!=0) {
	die(json_encode(array("status"=>"failure","reason"=>"交易已关闭.")));
}


$user = $_REQUEST['user'];

if (!auth($user,$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"用户名/密码错误")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	$errid = generateID();
	errLog("[DB-".$errid."]","MySQL Connect (trade.php): ".$con->connect_error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"无法连接至数据库.(错误:".$errid.")")));
}

// Get transaction details
$name = $_REQUEST['name'];
$amt = intval($_REQUEST['amt']);
$req_price = floatval(($_REQUEST['price']));


// Get account info
$sql = "SELECT * FROM Users WHERE name='$user'";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	$errid = generateID();
	errLog("[DB-".$errid."]","MySQL did not return (trade.php): ".$con->error."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"数据库未返回.(错误:".$errid.")")));
}
$row = $result->fetch_assoc();
$balance_stk = intval($row['balance_stock']);
$balance_fut = intval($row['balance_futures']);
$balance_cur = intval($row['balance_currency']);
$data = json_decode($row['data'],true);


// Get stock info
$sql = "SELECT * FROM Stocks WHERE name='$name'";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	$errid = generateID();
	errLog("[DB-".$errid."]","MySQL did not return (trade.php): ".$con->error."\nRequests:\n".json_encode($_REQUEST));
	die(DB(array("status"=>"failure","reason"=>"数据库未返回.(错误:".$errid.")")));
}
$row = $result->fetch_assoc();
$stock_stats = json_decode($row["stats"],true);

// Validate requested price
$price = floatval($row['price']);
if (abs($price-$req_price)>0.1) {
	$old_price = floatval($row['old_price']);
	if (true || abs($old_price-$req_price)>0.1) { // Do NOT allow trading with old price
		$errid = generateID();
		errLog("[InvalidRequest-".$errid."]","Stock price inconsistent (trade.php): Reqested:".$req_price." Acceptable:".$price."/".$old_price."\nRequests:\n".json_encode($_REQUEST));
		die(json_encode(array("status"=>"failure","reason"=>"请求价格与数据库不匹配,价格已过期.(错误:".$errid.")")));
	}
}
// Reqested price accepted.

// Calculate involved amount
if (strcasecmp($_REQUEST['action'], "sell")==0) {
	$amt *= -1;
}

$stk_profile=false;
foreach ($data['stocks'] as $stock) {
	if (strcmp($stock['name'],$name)==0) {
		$stk_profile = $stock;
	}
}

if (!$stk_profile) {
	$stk_profile = array("amt"=>0, "price0"=>0);
}

if (strcasecmp($row['type'], "STK")==0) {
	$total = $amt * $req_price;
	$balance_stk -= $total;
} else if (strcasecmp($row['type'], "FUT")==0 || strcasecmp($row['type'], "CUR")==0){
	if ($stk_profile['amt']==0) {
		$balance_fut -= abs($amt)*$req_price/$row['leverage'];

	} else {
		$balance_fut += abs($amt)*$stk_profile['price0']/$row['leverage']-($amt)*($req_price-$stk_profile['price0']);
	}
} else {
	$errid = generateID();
	errLog("[InvalidRequest-$errid]","Unrecognized type (trade.php): $row[type] Request: ".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"服务器内部错误, 未知的证券类型. 请联系管理员: $errid")));
}

// Check if affordable
if ($balance_stk<0 || $balance_fut<0|| $balance_cur<0) {
	// Reject.
	$errid = generateID();
	errLog("[InvalidRequest-".$errid."]","Buy more stocks than affordable (trade.php): Purchased:".$total." Balance:".($balance+$total)."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"超出余额.(错误:".$errid.")")));
}

// Calculate holdings
$holding = 0;
$found = false;
foreach ($data['stocks'] as $stock) {
	if (strcmp($stock['name'],$name)==0) {
		$holding = intval($stock['amt']);
		$found = true;
		break;
	}
}

for ($i=0; $i < count($data['stocks']); $i++) {
	if (strcmp($data['stocks'][$i]['name'],$name)==0) {
		$holding = intval($data['stocks'][$i]['amt']);
		$data['stocks'][$i]['amt']=$holding+$amt;
		if ($holding==0) {
			// Own nothing before trading: write original price
			$data['stocks'][$i]['price0']=$req_price;
		} else if ($holding+$amt==0) {
			// Empting, erase original price to prevent from rendering
			unset($data['stocks'][$o]['price0']);
		}
		break;
	}
}

// Create new entry for that stock
if (!$found) {
	array_push($data['stocks'], array("name"=>$name,"amt"=>$amt,"price0"=>$req_price));
}

// Check if selling more than owned or buying more than owed
if (($holding+$amt)*$holding<0 || (strcasecmp($row['type'],"STK")==0 && $holding+$amt<0)) {
	// Reject.
	$errid = generateID();
	errLog("[InvalidRequest-".$errid."]","Sell more stocks than owned (trade.php): Sell:".(-$amt)." Owns:".($holding-$amt)."\nRequests:\n".json_encode($_REQUEST));
	die(json_encode(array("status"=>"failure","reason"=>"卖出数量超出所有.(错误:".$errid.")")));
}

$holding += $amt;

// Commit to database
$stock_stats["total_count"]++;
$stock_stats["local_count"]++;
$stock_stats["total_amt"] += abs($amt);
$stock_stats["local_amt"] += abs($amt);
if ($amt > 0) {
	// Buy
	$stock_stats["total_count_buy"] ++;
	$stock_stats["local_count_buy"] ++;
	$stock_stats["total_amt_buy"] += abs($amt);
	$stock_stats["local_amt_buy"] += abs($amt);
} else {
	// Sell
	// Statistic values are absolute values
	$stock_stats["total_count_sell"] ++;
	$stock_stats["local_count_sell"] ++;
	$stock_stats["total_amt_sell"] += abs($amt);
	$stock_stats["local_amt_sell"] += abs($amt);
}


trace("[Trade]".$user,$name."\t".$_REQUEST['action']."\t".$amt."\tat price\t".$req_price);
$sql = "UPDATE Users
SET balance_stock=$balance_stk,
balance_futures=$balance_fut,
balance_currency=$balance_cur,
data='".json_encode($data)."'
WHERE name='$user'";
if (!$con->query($sql)) {
	$errid = generateID();
	errLog("[DB-$errid]","MySQL Rejected transaction. (trade.php): Error: ".$con->error);
	die(json_encode(array("status"=>"failure","reason"=>"MySQL数据库错误. 请联系管理员: $errid")));
}

$sql = "UPDATE Stocks
SET stats = '".json_encode($stock_stats)."'
WHERE name='$name'";

if (!$con->query($sql)) {
	$errid = generateID();
	errLog("[DB-$errid]","MySQL Rejected updating statistics. (trade.php): Error: ".$con->error);
	die(json_encode(array("status"=>"failure","reason"=>"交易已成功完成. 但出现MySQL数据库错误. 请联系管理员: $errid")));
}

$con->close();
die(json_encode(array("status"=>"success")));
