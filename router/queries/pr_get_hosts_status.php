<?php
	
$qry_hosts = mysql_query("
	
	select
		h.id_host,
		h.active,
		h.ip_address,
		h.mac_address,
		h.hostname,
		h.hostname_friendly,
		
		h.is_online,
		h.date_last_seen,
		
		h.is_new,
		
		h.device_type,
		h.os,
		h.os_details,
		
		h.downloaded_now,
		h.uploaded_now,
		h.downloaded_today,
		h.uploaded_today,
		h.downloaded_month,
		h.uploaded_month,
		
		h.id_category,
		c.description as category
		
	from t_host h
	left join t_category c on c.id_category = h.id_category
	
	
	order by
		ifnull(c.ip_range_start, 999),
		h.ip_address
		
		
	", $conn);
	
?>