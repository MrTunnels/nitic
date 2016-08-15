<?php

function logToFile($file, $title, $msg) {
	$logfile = fopen($file, "a") or die("Unable to open log");
	$title = "<".date("l jS \of F Y h:i:s A")."> ".$title;
	fwrite($logfile, $title."\t");
	fwrite($logfile, $msg."\n");
	fclose($logfile);
}

function errLog($title, $msg) {
	logToFile("server.log", $title, $msg);
}

function trace($title, $detail) {
	logToFile("transactions.log", $title, $detail);
}

function generateID() {
	return substr(sha1(date("U")), 0, 6);
}
