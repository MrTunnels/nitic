<?php
header("Content-Type:text/plain");
require_once "consts.php";

/*
if (strcmp(file_get_contents("init_status"), "done")) {
	die("You may not initialize twice.");
}
// Creating database will fail anyway
*/

$con = new mysqli($db_server, $db_user, $db_pswd);

if ($con->connect_error) {
	die("Fail: Connect to database - ".$con->connect_error);
}

$sql = "CREATE DATABASE $db_name";
if ($con->query($sql) === TRUE) {
    echo "Database created successfully.\n";
} else {
    echo "Error creating database: " . $con->error;
}

$con->close();

$con = new mysqli($db_server, $db_user, $db_pswd, $db_name);
if ($con->connect_error) {
	die("Fail: Connect to database - ".$con->connect_error);
}

if (!$con->set_charset("utf8")) {
    die("Fail: Set Charset - ".$con->error);
}

$sql = "CREATE TABLE Users (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(32),
pswd VARCHAR(32),
type VARCHAR(16),
balance_stock INT,
balance_futures INT,
data LONGTEXT
)DEFAULT CHARSET=utf8";
if ($con->query($sql) !== TRUE) {
	die("Fail: Create Table Users - ".$con->error);
}

$sql = "CREATE TABLE Stocks (
name VARCHAR(64) PRIMARY KEY,
description TEXT,
price FLOAT,
new_price FLOAT,
old_price FLOAT,
data LONGTEXT,
stats LONGTEXT
)DEFAULT CHARSET=utf8";
if ($con->query($sql) !== TRUE) {
	die("Fail: Create Table Stocks - ".$con->error);
}

/*

$sql = "CREATE TABLE Futures (
name VARCHAR(64) PRIMARY KEY,
description TEXT,
priceh FLOAT,
pricel FLOAT,
new_priceh FLOAT,
new_pricel FLOAT,
old_priceh FLOAT,
old_pricel FLOAT,
data LONGTEXT
)DEFAULT CHARSET=utf8";
if ($con->query($sql) !== TRUE) {
	die("Fail: Create Table Futures - ".$con->error);
}
*/

$init_data = json_encode(array("stocks"=>array()));
$sql = "INSERT INTO Users (name,pswd,type,balance,data)
VALUES('$admin_user','$admin_pswd','admin',0,'$init_data')";
if (!$con->query($sql)){
	die("Could not create admin account: "+$con->error);
}

$con->close();
echo "Initialization Sequence Completed.";
?>
