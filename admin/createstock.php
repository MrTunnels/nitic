<?php

require_once 'userctrl.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL connect error (admin/createstock.php) :".$con->connect_error)));
}
$con->set_charset("utf8");

$id = $_REQUEST['name'];
$type = $_REQUEST['type'];
$description = $_REQUEST['description'];
$price = $_REQUEST['price'];
$leverage = $_REQUEST['leverage'];
$init_data = json_encode(array("trace"=>array()));
$init_stats = json_encode(array(
						"total_count"=>0, "local_count"=>0, "total_amt"=>0, "local_amt"=>0,
						"total_count_buy"=>0,"local_count_buy"=>0,"total_amt_buy"=>0,"local_amt_buy"=>0,
						"total_count_sell"=>0,"local_count_sell"=>0,"total_amt_sell"=>0,"local_amt_sell"=>0
                        ));

$sql = "INSERT INTO Stocks (name,type,description,leverage,price,new_price,old_price,data,stats)
VALUES ('$id','$type','$description',$leverage,$price,$price,$price,'$init_data','$init_stats')";

$con->query($sql);

die(json_encode(array("status"=>"success")));
