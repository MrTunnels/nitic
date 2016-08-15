<?php

require_once 'userctrl.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL connect error (admin/update.php) :".$con->connect_error)));
}
$con->set_charset("utf8");

// Update stocks
$sql = "SELECT name,price,new_price,data,stats FROM Stocks";
$result = $con->query($sql);
while($row = $result->fetch_assoc()) {
	$id = $row['name'];
	$old_price = $row['price'];
	$price = $row['new_price'];
	$data = json_decode($row['data'],true);
	array_push($data['trace'], $price);

	$stats = json_decode($row['stats'],true);
	$stats["local_amt"] = 0;
	$stats["local_count"] = 0;
	$stats["local_amt_buy"] = 0;
	$stats["local_count_buy"] = 0;
	$stats["local_amt_sell"] = 0;
	$stats["local_count_sell"] = 0;

	$sql = "UPDATE Stocks
	SET old_price=$old_price,price=$price,data='".json_encode($data)."',stats='".json_encode($stats)."'
	WHERE name='$id'";
	$con->query($sql);
}

die(json_encode(array("status"=>"success")));

