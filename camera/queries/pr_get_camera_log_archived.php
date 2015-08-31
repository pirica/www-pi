<?php
	
$qry_camera_log = mysql_query("
	
	select
		cl.id_camera_log_archive,
		cl.date,
		cl.time,
		cl.hour_lbl,
		cl.time_value,
		cl.name
		
	from t_camera_log_archive cl
	where
		cl.date = '" . $date . "'
		and cl.hour_lbl = ifnull(" . ($time == 'all' ? 'null' : "'".$time."'") . ", cl.hour_lbl)
		and ifnull(status,0) = 0
	
	order by
		cl.name
		
		
	");
	
?>