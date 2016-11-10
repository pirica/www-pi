<?php

$date = 1412380800; // start of logging: 2014100400
$i = 0;
$range_start_sql = date("YmdH", $date);

while ($range_start_sql < date("YmdH"))
{
	mysqli_query($conn, "
		
		insert into t_usage_today
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
			concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d %H') ) as usagekey,
			h.mac_address,
			d.date as date_usage,
			0,
			0,
			0,
			0
		from 
		(
			SELECT (str_to_date('" . $range_start_sql . "', '%Y%m%d%H')+INTERVAL (H+T+U) hour) date
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
				DATE_FORMAT((str_to_date('" . $range_start_sql . "', '%Y%m%d%H')+INTERVAL (H+T+U) hour), '%Y%m%d%H') < '" . date("YmdH") . "'
		
		) d
		cross join (
			select mac_address from t_host
			where active = 1
			group by mac_address
		) h
		left join t_usage_today u on u.usagekey = concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d %H') )
		
		where
			u.usagekey is null
		
	");
	
	$i++;
	$date = strtotime('+' . $i . ' day', $date);
	$range_start_sql = strtotime('+' . $i . '0 day', $date);
}



$date = 1412380800; // start of logging: 2014100400
$i = 0;
$range_start_sql = date("Ymd", $date);

while ($range_start_sql < date("Ymd"))
{
	mysqli_query($conn, "
		
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
				DATE_FORMAT((str_to_date('" . $range_start_sql . "', '%Y%m%d')+INTERVAL (H+T+U) day), '%Y%m%d') < '" . date("Ymd") . "'
		
		) d
		cross join (
			select mac_address from t_host
			where active = 1
			group by mac_address
		) h
		left join t_usage_day u on u.usagekey = concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d') )
		
		where
			u.usagekey is null
		
	");

	$i++;
	$date = strtotime('+' . $i . ' month', $date);
	$range_start_sql = date("Ymd", $date);
}


$date = 1412380800; // start of logging: 2014100400
$i = 0;
$range_start_sql = date("Ym04", $date);

while ($range_start_sql < date("Ym04"))
{
	mysqli_query($conn, "
		
		insert into t_usage_month
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
			concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-04') ) as usagekey,
			h.mac_address,
			d.date as date_usage,
			0,
			0,
			0,
			0
		from 
		(
			SELECT (str_to_date('" . $range_start_sql . "', '%Y%m%d')+INTERVAL (H+T+U) month) date
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
				DATE_FORMAT((str_to_date('" . $range_start_sql . "', '%Y%m%d')+INTERVAL (H+T+U) month), '%Y%m04') < '" . date("Ym04") . "'
		
		) d
		cross join (
			select mac_address from t_host
			where active = 1
			group by mac_address
		) h
		left join t_usage_month u on u.usagekey = concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-04') )
		
		where
			u.usagekey is null
		
	");

	$i++;
	$date = strtotime('+' . $i . ' year', $date);
	$range_start_sql = date("Ym04", $date);
}

	
	
?>