<?php
	$source_money=$_GET['source'];
	$target_money=$_GET['target'];
	//echo $_GET['source'];
    $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/currencyservice/currency?fromCurrency='.$source_money.'&toCurrency='.$target_money.'&amount=1';
	//echo $url;
    $header = array(
                    'apikey:0c9a1f409f9ad15df7c263323f5266ac',
                    );
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);
    $data = json_decode($res);
    echo $data->retData->currency;
    
    ?>


