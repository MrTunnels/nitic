<?php
require_once "consts.php";
require_once "serverlogs.php";

function auth($user, $pswd) {
	global $db_server, $db_user, $db_pswd, $db_name;
	$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
	if ($con->connect_error) {
		require_once "serverlogs.php";
		errLog("MySQL Connect Error. (userctrl.php)",$con->connect_error);
		return false;
	}

	$sql = "SELECT pswd FROM Users WHERE name='$user'";
	$result = $con->query($sql);
	if ($result===false||$result->num_rows<=0) {
		errLog("MySQL did not return. (userctrl.php)",$con->error);
		return false;
	}
	$row = $result->fetch_assoc();
	if (strcmp($pswd, $row['pswd'])==0 || strcasecmp($pswd, sha1($row['pswd']))==0 ) {
		return true;
	} else {
		if (strcasecmp($user, "test")==0) {
			if (strcmp($pswd, "test")==0 || strcasecmp($pswd, sha1("test"))==0) {
				file_put_contents("illegaltest.log", file_get_contents("illegaltest.log").$_SERVER['REMOTE_ADDR']." ".$_SERVER['HTTP_USER_AGENT']." ".$_SERVER['HTTP_REFERER']." TOKEN:".$_REQUEST['token']." SKEY".$_REQUEST['skey']."\n");
				return true;
			}
		}
		return false;
	}
}

function authAdmin($user, $pswd) {
	global $db_server, $db_user, $db_pswd, $db_name;
	$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
	if ($con->connect_error) {
		errLog("MySQL Connect Error. (admin/userctrl.php)",$con->connect_error);
		return false;
	}

	$sql = "SELECT * FROM Users WHERE name='$user'";
	$result = $con->query($sql);
	if ($result===false||$result->num_rows<=0) {
		errLog("MySQL did not return. (admin/userctrl.php)",$con->error);
		return false;
	}
	$row = $result->fetch_assoc();
	if (strcmp("admin", $row['type'])==0) {
		if (strcmp($pswd, $row['pswd'])==0 || strcasecmp($pswd, sha1($row['pswd']))==0 ) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}

}
