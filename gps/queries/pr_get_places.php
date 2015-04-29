<?php
	
$qry_places = mysql_query("
	
	select
		p.id_place,
		p.description,
		p.pre_description,
		p.lat_top,
		p.lon_right,
		p.lat_bottom,
		p.lon_left
		
	from t_place p
	where
		p.active = 1
	
	order by
		p.description
		
		
	");
	
?>