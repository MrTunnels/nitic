<?php

require_once 'userctrl.php';
require_once 'serverlogs.php';

if (!auth($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL connect error (getMoneyList.php) :".$con->connect_error)));
}
$con->set_charset("utf8");
$sql = "SELECT * FROM `MoneyType`";
$result = $con->query($sql);
if ($result||$result->num_rows<=0) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQLÎ´·µ»Ø (getMoneyList.php)")));
}

die(json_encode(array("status"=>"success","text_1"=>$result->num_rows)));
	$return_text="";
	while($row = $result->fetch_assoc()){
		$return_text=$return_text."<code>".$row['Name']."</code>   ".$row['description']."</ br>";
    }
	die(json_encode(array("status"=>"success","text_1"=>$return_text)));

	
	
