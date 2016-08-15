<?php
	$currencynow="";
	$Money_Type=array('EUR','USD','CNY','GBP');
	foreach($Money_Type as $Source_Money)
	{
		foreach($Money_Type as $Target_Money)
		{
			if($Source_Money != $Target_Money)
			{
				$currencynow= file_get_contents('http://app.starrystudio.org/currency.php?source='.$Source_Money.'&target='.$Target_Money);
				
				if ($currencynow=="")
				{
					echo("正在获取汇率");
				}
				else
				{
					echo("<code>1</code> ".$Source_Money."兑<code> ".$currencynow."</code>".$Target_Money."<br />\n");
				}
			}
		}
	}
?>