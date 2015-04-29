<?php

include 'connection.php';

$time = '';
$lat = '';
$lon = '';
$speed = '';
$accuracy = '';
$heading = '';
$interval = '';
$user = '';

if(isset($_GET['time']) && $_GET['time'] != '' && is_numeric($_GET['time'])){
	$time = $_GET['time'] / 1000;
}
if(isset($_GET['lat']) && $_GET['lat'] != '' && is_numeric($_GET['lat'])){
	$lat = $_GET['lat'];
}
if(isset($_GET['lon']) && $_GET['lon'] != '' && is_numeric($_GET['lon'])){
	$lon = $_GET['lon'];
}
if(isset($_GET['speed']) && $_GET['speed'] != '' && is_numeric($_GET['speed'])){
	$speed = $_GET['speed'];
}
if(isset($_GET['accuracy']) && $_GET['accuracy'] != '' && is_numeric($_GET['accuracy'])){
	$accuracy = $_GET['accuracy'];
}
if(isset($_GET['heading']) && $_GET['heading'] != '' && is_numeric($_GET['heading'])){
	$heading = $_GET['heading'];
}
/*if(isset($_GET['interval']) && $_GET['interval'] != '' && is_numeric($_GET['interval'])){
	$interval = $_GET['interval'];
}*/
if(isset($_GET['user']) && $_GET['user'] != ''){
	$user = mysql_real_escape_string($_GET['user']);
}

if($lat != '' && $lon != '' && $speed != '' && $accuracy != '' && $heading != ''){
	mysql_query("insert into t_log_track (lat, lon, speed, accuracy, heading, time, username) values (".$lat.", ".$lon.", ".$speed.", ".$accuracy.", ".$heading.", '".date('Y-m-d H:i:s', $time)."', '".$user."')");// or die("can't insert into db<br>");
	
	/*
	$fh = fopen('position.json', 'w') or die("can't open file<br>");
	fwrite($fh, '{"lat": "'.$lat.'", "lon": "'.$lon.'", "speed": "'.$speed.'", "accuracy":"'.$accuracy.'", "heading":"'.$heading.'", "time":"'.$time.'"}');
	fclose($fh);
	*/
}

?>