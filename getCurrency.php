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
$sql = "SELECT * FROM `Currency`";
$result = $con->query($sql);
if ($result===false||$result->num_rows<=0) {
	die(json_encode(array("status"=>"failure","reason"=>"MySQL未返回 (getMoneyList.php)")));
}

	$return_text="";
	while($row = $result->fetch_assoc()){
		
		$ch = curl_init();
		$url = 'http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency='.$row['MoneySource'].'&toCurrency='.$row['MoneyTarget'].'&amount=1';
		//echo $url;
		$header = array(
						'apikey:0c9a1f409f9ad15df7c263323f5266ac',
						);
		curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($ch , CURLOPT_URL , $url);
		$res = curl_exec($ch);
		$data = json_decode($res);
		$float=floatval($data->retData->currency);
		$float=round($float,4);
		//$float=trim($float,'0.0000');
		$return_text=$return_text."<code>1</code>".$row['MoneySource']."兑换<code>".$float."</code>".$row['MoneyTarget']."<br />";
    }
die(json_encode(array("status"=>"success","text_1"=>$return_text)));

	
	
