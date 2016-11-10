<?php
if(!isset($mysql_host)){
	require '../connection.php';
}

$date = time(); // current hour, since this is 'now'

$range_start_sql = date("Ym" . $tm_start0, strtotime('-1 month', $date));
$range_end_sql = date("Ym" . $tm_start0, strtotime('+1 month', $date));

$range_month = date("m", $date);

$night_start_sql = '0000';
$night_end_sql = '1000';

mysqli_query($conn, "
	
	replace into t_usage_month (usagekey, mac_address, date_usage, downloaded, uploaded, downloaded_telemeter, uploaded_telemeter)
	select
		concat(hu.mac_address , DATE_FORMAT(hu.date_usage, '%Y-%m-" . $tm_start0 . "') ) as usagekey,
		
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-" . $tm_start0 . "'), '%Y-%m-%d') as date_usage,
		
		sum(ifnull(hu.downloaded,0)) as downloaded,
		sum(ifnull(hu.uploaded,0)) as uploaded,
		
		sum(ifnull(hu.downloaded_telemeter,0)) as downloaded_telemeter,
		sum(ifnull(hu.uploaded_telemeter,0)) as uploaded_telemeter
		
	from t_usage_day hu
	where
		DATE_FORMAT(hu.date_usage, '%Y%m%d') >= '" . $range_start_sql . "'
		and DATE_FORMAT(hu.date_usage, '%Y%m%d') < '" . $range_end_sql . "'
	
	group by
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-" . $tm_start0 . "'), '%Y-%m-%d')
	
");

	
?>