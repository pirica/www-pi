<?php

echo "<!--\n";

$sql_hosts_usage = "
    select
		hu.mac_address
   
	from t_usage_month hu
	where
			DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') >= '" . $range_start_sql . "'
		and DATE_FORMAT(hu.date_usage, '" . $date_range_format . "') < '" . $range_end_sql . "'
		and ifnull(hu.downloaded,0) + ifnull(hu.uploaded,0) > 0
	group by
		hu.mac_address
    ";

echo $sql_hosts_usage;
echo ";\n\n";

$qry_hosts_usage = mysqli_query($conn, $sql_hosts_usage);

?>