<?php

require_once 'userctrl.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied.")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL Connect Error (setprice.php) :".$con->connect_erro)));
}

$name=$_REQUEST['name'];

$price = $_REQUEST['price'];
$sql = "UPDATE Stocks
SET new_price=$price
WHERE name='$name'";
$con->query($sql);

die(json_encode(array("status"=>"success")));
