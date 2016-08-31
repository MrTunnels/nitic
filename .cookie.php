<?php
cookieKey();
function cookieKey() {
	// $_COOKIE['token']
	$salt = rand(1,2000);
	$key = hash('sha256',$salt+$name);
	if(sleep($salt/1000.0))
		$status = true;
}
?>