<?php


	
mysql_query("
	
	update t_host
	set
		downloaded_now = 0,
		uploaded_now = 0,
		downloaded_today = 0,
		uploaded_today = 0,
		downloaded_month = 0,
		uploaded_month = 0
		
", $conn);
	
mysql_query("
	
	update t_host h
	join (
		select
			sum(ifnull(hun.usage_peak_in,0)) as downloaded_now,
			sum(ifnull(hun.usage_peak_out,0)) as uploaded_now,
			h.mac_address
			
		from t_host h
		join t_host_usage hun on hun.mac_address = h.mac_address
			and hun.date_usage >= now() - interval 5 minute
		where
			h.active = 1
		group by
			h.mac_address
		
	) hu on hu.mac_address = h.mac_address
	set
		h.downloaded_now = hu.downloaded_now,
		h.uploaded_now = hu.uploaded_now
	where
		h.mac_address = hu.mac_address
		
", $conn);
	
mysql_query("
	
	update t_host h
	join (
		select
			sum(ifnull(hud.usage_peak_in,0)) as downloaded_today,
			sum(ifnull(hud.usage_peak_out,0)) as uploaded_today,
			h.mac_address
			
		from t_host h
		join t_host_usage hud on hud.mac_address = h.mac_address
			and hud.date_usage >= date_format(now(), '%Y-%m-%d')
		where
			h.active = 1
		group by
			h.mac_address
		
	) hu on hu.mac_address = h.mac_address
	set
		h.downloaded_today = hu.downloaded_today,
		h.uploaded_today = hu.uploaded_today
		
	where
		h.mac_address = hu.mac_address
		
", $conn);
	
	
mysql_query("
	
	update t_host h
	join (
		select
			sum(ifnull(hum.usage_peak_in,0)) as downloaded_month,
			sum(ifnull(hum.usage_peak_out,0)) as uploaded_month,
			h.mac_address
			
		from t_host h
		join t_host_usage hum on hum.mac_address = h.mac_address
			and hum.date_usage >= case when date_format(now(), '%d') < " . $tm_start . " then date_format(now(), '%Y-%m-" . $tm_start0 . "') - interval 1 month else date_format(now(), '%Y-%m-" . $tm_start0 . "') end
		where
			h.active = 1
		group by
			h.mac_address
		
	) hu on hu.mac_address = h.mac_address
	set
		h.downloaded_month = hu.downloaded_month,
		h.uploaded_month = hu.uploaded_month
		
	where
		h.mac_address = hu.mac_address
		
", $conn);
	
?>