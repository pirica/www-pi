<?php

include 'connection.php';

$time = '';

if(isset($_GET['time']) && $_GET['time'] != '' && is_numeric($_GET['time'])){
	$time = $_GET['time'];
}

if($time != ''){
	$qry_log = mysql_query("select * from t_log_track where id_log_track = ".$time." or id_log_track = ".($time+1)." order by id_log_track desc");
	
	$nexttime = '';
	$json = '';
	
	while ($row = mysql_fetch_array($qry_log)) {
		if($nexttime == ''){
			$nexttime = $row{'id_log_track'};
		}
		else {
			$json = '{"lat": "'.$row{'lat'}.'", "lon": "'.$row{'lon'}.'", "speed": "'.$row{'speed'}.'", "accuracy":"'.$row{'accuracy'}.'", "heading":"'.$row{'heading'}.'", "time":"'.$row{'time'}.'", "nexttime":"'.$nexttime.'"}';
		}
	}
	
	echo $json;
}

?>