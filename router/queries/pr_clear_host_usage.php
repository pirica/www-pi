<?php


$date = time(); // current hour, since this is 'today'

//$range_start_sql = date("Ymd00", strtotime('-2 month', $date));
$range_end_sql = date("Ym" . $tm_start0, strtotime('-2 month', $date));

mysql_query("
	
	delete from t_host_usage
	where
		DATE_FORMAT(date_usage, '%Y%m%d') < '" . $range_end_sql . "'
	
", $conn);

	
?>