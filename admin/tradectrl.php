<?php

require_once 'userctrl.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission denied.")));
}

if (strcmp($_REQUEST['action'],"stop")==0) {
	file_put_contents("tradestatus", "closed");
} else if (strcmp($_REQUEST['action'],"start")==0) {
	file_put_contents("tradestatus", "open");
} else if (strcmp($_REQUEST['action'],"restart")==0) {
	file_put_contents("tradestatus", "open");
	$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
	if ($con->connect_error) {
		die(json_encode(array("status"=>"failure","reason"=>"MySQL Connect error (admin/tradectrl.php): ".$con->connect_error)));
	}
	$init_data = json_encode(array("trace"=>array()));
	$init_stats = json_encode(array(
						"total_count"=>0, "local_count"=>0, "total_amt"=>0, "local_amt"=>0,
						"total_count_buy"=>0,"local_count_buy"=>0,"total_amt_buy"=>0,"local_amt_buy"=>0,
						"total_count_sell"=>0,"local_count_sell"=>0,"total_amt_sell"=>0,"local_amt_sell"=>0
                        ));

	$sql = "UPDATE Stocks
	SET data='$init_data', stats='$init_stats'";
	$con->query($sql);
} else {
	die(json_encode(array("status"=>"failure","reason"=>"No command.")));
}
die(json_encode(array("status"=>"success")));

