<?php
	
$qry_categories = mysql_query("
	
	select
		c.id_category,
		c.description as category
		
	from t_category c
	where
		c.active = 1
	
	order by
		ifnull(c.ip_range_start, 999)
		
		
	", $conn);
	
?>