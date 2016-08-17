<?php


require_once 'userctrl.php';
require_once 'serverlogs.php';

if (!authAdmin($_REQUEST['user'],$_REQUEST['pswd'])) {
	die(json_encode(array("status"=>"failure","reason"=>"Permission Denied")));
}

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL connect error (admin/updateMoneyList.php) :".$con->connect_error)));
}
$con->set_charset("utf8");

$sql = "SELECT * FROM `MoneyType`";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL未返回 (updateCurrencyList.php)")));
}
if ($result->num_rows==1) {
	die(json_encode(array("status"=>"failure","reason"=>"你只有一个种货币怎么换。。。 (getMoneyList.php)")));
}
$sql = "DELETE FROM `Currency`";
$con->query($sql);
$MoneyType = array();

while($row = $result->fetch_assoc()){
	array_push($MoneyType,$row['Name']);
}
$id=1;
foreach($MoneyType as $MoneySource)
{
	foreach($MoneyType as $MoneyTarget)
	{
		if($MoneySource!=$MoneyTarget)
		{
		$ch = curl_init();
		$url = 'http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency='.$MoneySource.'&toCurrency='.$MoneyTarget.'&amount=1';
		$header = array(
						'apikey:0c9a1f409f9ad15df7c263323f5266ac',
						);
		curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($ch , CURLOPT_URL , $url);
		$res = curl_exec($ch);
		$data = json_decode($res);
		$price = $data->retData->currency;
		$leverage = 1;//???
		$init_data = json_encode(array("trace"=>array()));
		$init_stats = json_encode(array(
						"total_count"=>0, "local_count"=>0, "total_amt"=>0, "local_amt"=>0,
						"total_count_buy"=>0,"local_count_buy"=>0,"total_amt_buy"=>0,"local_amt_buy"=>0,
						"total_count_sell"=>0,"local_count_sell"=>0,"total_amt_sell"=>0,"local_amt_sell"=>0
                        ));
		$sql = "INSERT INTO Currency (id,MoneySource,MoneyTarget,leverage,price,new_price,old_price,data,status) VALUES ('$id','$MoneySource','$MoneyTarget',$leverage,$price,$price,$price,'$init_data','$init_stats')";

		$con->query($sql);
		$id=$id+1;
		}
	}
}
die(json_encode(array("status"=>"success","id"=>$sql)));

	
	
