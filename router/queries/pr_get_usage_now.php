<?php

echo "<!--\n";

$sql_max = "
    select
         max(total) as max_total,
        max(downloaded) as max_down,
        max(uploaded) as max_up,
        max(case when downloaded > uploaded then downloaded else uploaded end) as max_both,
        max(total) as max_all
    from (
    select
		DATE_FORMAT(hu.date_usage, '" . $date_period_format . "') as date_usage,
		
		sum(ifnull(hu.downloaded,0)) as downloaded,
		sum(ifnull(hu.uploaded,0)) as uploaded,
		sum(ifnull(hu.downloaded,0) + ifnull(hu.uploaded,0)) as total
		
	from t_usage_now hu
	where
			DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') >= '" . $range_start_sql . "'
		and DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') < '" . $range_end_sql . "'
	
	group by
		DATE_FORMAT(hu.date_usage, '" . $date_period_format . "')
    ) m
    ";

echo $sql_max;
echo ";\n\n";

$qry_max = mysql_query($sql_max, $conn);
	
$sql_totals = "
	select
		DATE_FORMAT(hu.date_usage, '" . $date_period_format . "') as date_usage,
		DATE_FORMAT(hu.date_usage, '" . $date_label_format . "') as date_usage_label,
		
		sum(ifnull(hu.downloaded,0)) as downloaded,
		sum(ifnull(hu.uploaded,0)) as uploaded,
		sum(ifnull(hu.downloaded,0) + ifnull(hu.uploaded,0)) as total,
		
		sum(ifnull(hu.downloaded_telemeter,0)) as downloaded_telemeter,
		sum(ifnull(hu.uploaded_telemeter,0)) as uploaded_telemeter,
		sum(ifnull(hu.downloaded_telemeter,0)) + ifnull(hu.uploaded_telemeter,0) as total_telemeter
		
	from t_usage_now hu
	where
		DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') >= '" . $range_start_sql . "'
		and DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') < '" . $range_end_sql . "'
	
	group  by
		DATE_FORMAT(hu.date_usage, '" . $date_period_format . "'),
		DATE_FORMAT(hu.date_usage, '" . $date_label_format . "')
		
	order by
		DATE_FORMAT(hu.date_usage, '" . $date_period_format . "')
		
	";

echo $sql_totals;
echo ";\n\n";
	
$qry_totals = mysql_query($sql_totals, $conn);
	
$sql_hosts = "
	
	select
		h.id_host,
		h.ip_address,
		h.mac_address,
		ifnull(h.hostname_friendly,h.hostname) as hostname,
		h.id_category,
		c.description as category,
		
        DATE_FORMAT(hu.date_usage, '" . $date_period_format . "') as date_usage,
		DATE_FORMAT(hu.date_usage, '" . $date_label_format . "') as date_usage_label,
		
		ifnull(hu.downloaded,0) as downloaded,
		ifnull(hu.uploaded,0) as uploaded,
		ifnull(hu.downloaded,0) + ifnull(hu.uploaded,0) as total,
		
		ifnull(hu.downloaded_telemeter,0) as downloaded_telemeter,
		ifnull(hu.uploaded_telemeter,0) as uploaded_telemeter,
		ifnull(hu.downloaded_telemeter,0) + ifnull(hu.uploaded_telemeter,0) as total_telemeter
		
	from t_usage_now hu
	join t_host h on hu.mac_address = h.mac_address
		and h.active = 1
	left join t_category c on c.id_category = h.id_category
	
	where
		DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') >= '" . $range_start_sql . "'
		and DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') < '" . $range_end_sql . "'
	
	order by
		ifnull(c.ip_range_start, 999),
		h.ip_address,
		h.mac_address,
		DATE_FORMAT(hu.date_usage, '" . $date_period_format . "')
		
	
	";

echo $sql_hosts;
echo ";\n\n";
echo "-->";

$qry_hosts = mysql_query($sql_hosts, $conn);

?>