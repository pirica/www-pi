<?php
	
mysql_query("
	
	insert into t_camera_log_archive
	(
		date,
		time,
		hour_lbl,
		time_value,
		name,
		camera
	)
	select
		cl.date,
		cl.time,
		cl.hour_lbl,
		cl.time_value,
		cl.name,
		cl.camera
	from t_camera_log cl
	where
		date = '" . $date . "'
		and hour_lbl = ifnull(" . ($time == 'all' ? 'null' : "'".$time."'") . ", hour_lbl)
		and ifnull(status,0) = 0
	
		
	");
	
mysql_query("
	
	delete from t_camera_log
	where
		date = '" . $date . "'
		and hour_lbl = ifnull(" . ($time == 'all' ? 'null' : "'".$time."'") . ", hour_lbl)
	
		
	");
	
?>