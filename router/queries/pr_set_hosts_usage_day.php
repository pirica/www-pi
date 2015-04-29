<?php
if(!isset($mysql_host)){
	require '../connection.php';
}

$date = time(); // current hour, since this is 'today'

$range_start_sql = date("Ymd", strtotime('-1 month', $date));
$range_end_sql = date("Ymd", strtotime('+1 day', $date));

$night_start_sql = '0000';
$night_end_sql = '1000';
/*
mysql_query("
	
	insert into t_usage_day
	(
		usagekey,
		mac_address,
		date_usage,
		downloaded,
		uploaded,
		downloaded_telemeter,
		uploaded_telemeter
	)
	select
		concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d') ) as usagekey,
		h.mac_address,
		d.date as date_usage,
		0,
		0,
		0,
		0
	from 
    (
		SELECT (str_to_date('" . $range_start_sql . "', '%Y%m%d')+INTERVAL (H+T+U) day) date
		FROM ( SELECT 0 H
			UNION ALL SELECT 100 UNION ALL SELECT 200 UNION ALL SELECT 300
		  ) H 
		  CROSS JOIN ( SELECT 0 T
			UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
			UNION ALL SELECT  40 UNION ALL SELECT  50 UNION ALL SELECT  60
			UNION ALL SELECT  70 UNION ALL SELECT  80 UNION ALL SELECT  90
		  ) T 
		  CROSS JOIN ( SELECT 0 U
			UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
			UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
			UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
		  ) U
		WHERE
			DATE_FORMAT((str_to_date('" . $range_start_sql . "', '%Y%m%d')+INTERVAL (H+T+U) day), '%Y%m%d') < '" . $range_end_sql . "'
	
	) d
	#cross join (
	#	select mac_address from t_host_usage hu 
	#	where DATE_FORMAT(hu.date_usage, '%Y%m%d') >= '" . $range_start_sql . "'
	#	and DATE_FORMAT(hu.date_usage, '%Y%m%d') < '" . $range_end_sql . "'
	#	group by mac_address
	#) h
	cross join (
		select mac_address from t_host
		where active = 1
		group by mac_address
	) h
	left join t_usage_day u on u.usagekey = concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d') )
	
	where
		u.usagekey is null
	
", $conn);
*/
mysql_query("
	
	replace into t_usage_day (usagekey, mac_address, date_usage, downloaded, uploaded, downloaded_telemeter, uploaded_telemeter)
	select
		concat(hu.mac_address , DATE_FORMAT(hu.date_usage, '%Y-%m-%d') ) as usagekey,
		
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-%d'), '%Y-%m-%d') as date_usage,
		
		sum(ifnull(hu.downloaded,0)) as downloaded,
		sum(ifnull(hu.uploaded,0)) as uploaded,
		
		sum(ifnull(hu.downloaded_telemeter,0)) as downloaded_telemeter,
		sum(ifnull(hu.uploaded_telemeter,0)) as uploaded_telemeter
		
		#sum(
		#	case 
		#		when DATE_FORMAT(hu.date_usage, '%H%i') >= '" . $night_start_sql . "' 
		#		and DATE_FORMAT(hu.date_usage, '%H%i') < '" . $night_end_sql . "' 
		#		then 0.5 else 1.0
		#	end 
		#	* 
		#	ifnull(hu.downloaded,0)
		#) as downloaded_telemeter,
		
		#sum(
		#	case 
		#		when DATE_FORMAT(hu.date_usage, '%H%i') >= '" . $night_start_sql . "' 
		#		and DATE_FORMAT(hu.date_usage, '%H%i') < '" . $night_end_sql . "' 
		#		then 0.5 else 1.0
		#	end 
		#	* 
		#	ifnull(hu.uploaded,0)
		#) as uploaded_telemeter
	
		
	from t_usage_today hu
	where
		DATE_FORMAT(hu.date_usage, '%Y%m%d') >= '" . $range_start_sql . "'
		and DATE_FORMAT(hu.date_usage, '%Y%m%d') < '" . $range_end_sql . "'
	
	group by
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-%d'), '%Y-%m-%d')
	
", $conn);

	
?>