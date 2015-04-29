
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
		SELECT (str_to_date('2015040300', '%Y%m%d%H')+INTERVAL (D+H+T+U) hour) date
		FROM ( SELECT 0 D
			UNION ALL SELECT 1000 UNION ALL SELECT 2000 UNION ALL SELECT 3000
			UNION ALL SELECT 4000 UNION ALL SELECT 5000 UNION ALL SELECT 6000
			UNION ALL SELECT 7000 UNION ALL SELECT 8000 UNION ALL SELECT 9000
		  ) D
		  CROSS JOIN ( SELECT 0 H
			UNION ALL SELECT 100 UNION ALL SELECT 200 UNION ALL SELECT 300
			UNION ALL SELECT 400 UNION ALL SELECT 500 UNION ALL SELECT 600
			UNION ALL SELECT 700 UNION ALL SELECT 800 UNION ALL SELECT 900
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
			DATE_FORMAT((str_to_date('2015040300', '%Y%m%d%H')+INTERVAL (D+H+T+U) hour), '%Y%m%d%H') < '2015042300'
	
	) d
	#cross join (
	#	select mac_address from t_host_usage hu 
	#	where DATE_FORMAT(hu.date_usage, '%Y%m%d%H') >= '2015040300'
	#	and DATE_FORMAT(hu.date_usage, '%Y%m%d%H') < '2015042300'
	#	group by mac_address
	#) h
	cross join (
		select mac_address from t_host
		where active = 1
		group by mac_address
	) h
	left join t_usage_today u on u.usagekey = concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d %H') )
	
	where
		u.usagekey is null
	;
    
    
	replace into t_usage_today (usagekey, mac_address, date_usage, downloaded, uploaded, downloaded_telemeter, uploaded_telemeter)
	select
		concat(hu.mac_address , DATE_FORMAT(hu.date_usage, '%Y-%m-%d %H') ) as usagekey,
		
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-%d %H'), '%Y-%m-%d %H') as date_usage,
		
		sum(ifnull(hu.usage_peak_in,0)) as downloaded,
		sum(ifnull(hu.usage_peak_out,0)) as uploaded,
		
		sum(
			case 
				when DATE_FORMAT(hu.date_usage, '%H%i') >= '0000' 
				and DATE_FORMAT(hu.date_usage, '%H%i') < '1000' 
				then 0.5 else 1.0
			end 
			* 
			ifnull(hu.usage_peak_in,0)
		) as downloaded_telemeter,
		
		sum(
			case 
				when DATE_FORMAT(hu.date_usage, '%H%i') >= '0000' 
				and DATE_FORMAT(hu.date_usage, '%H%i') < '1000' 
				then 0.5 else 1.0
			end 
			* 
			ifnull(hu.usage_peak_out,0)
		) as uploaded_telemeter
	
		
	from t_host_usage hu
	where
		DATE_FORMAT(hu.date_usage, '%Y%m%d%H') >= '2015040300'
		and DATE_FORMAT(hu.date_usage, '%Y%m%d%H') < '2015042300'
	
	group by
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-%d %H'), '%Y-%m-%d %H')
	;
    
    
	replace into t_usage_day (usagekey, mac_address, date_usage, downloaded, uploaded, downloaded_telemeter, uploaded_telemeter)
	select
		concat(hu.mac_address , DATE_FORMAT(hu.date_usage, '%Y-%m-%d') ) as usagekey,
		
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-%d'), '%Y-%m-%d') as date_usage,
		
		sum(ifnull(hu.downloaded,0)) as downloaded,
		sum(ifnull(hu.uploaded,0)) as uploaded,
		
		sum(ifnull(hu.downloaded_telemeter,0)) as downloaded_telemeter,
		sum(ifnull(hu.uploaded_telemeter,0)) as uploaded_telemeter
		
		
	from t_usage_today hu
	where
		DATE_FORMAT(hu.date_usage, '%Y%m%d') >= '20150403'
		and DATE_FORMAT(hu.date_usage, '%Y%m%d') < '20150424'
	
	group by
		hu.mac_address,
		str_to_date(DATE_FORMAT(hu.date_usage, '%Y-%m-%d'), '%Y-%m-%d')
	;