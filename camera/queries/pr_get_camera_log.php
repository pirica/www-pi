<?php

if($time == 'all')
{
	$qry_camera_log = mysql_query("
		
		select
			cl.id_camera_log,
			cl.date,
			cl.time,
			cl.hour_lbl,
			cl.time_value,
			cl.name
			
		from t_camera_log cl
		where
			cl.date = '" . $date . "'
			and status = 0
		
		order by
			cl.name
			
			
		");
}
else {
	$qry_camera_log = mysql_query("
		
		select
			cl.id_camera_log,
			cl.date,
			cl.time,
			cl.hour_lbl,
			cl.time_value,
			cl.name
			
		from t_camera_log cl
		where
			cl.date = '" . $date . "'
			and cl.hour_lbl = '".$time."'
			and status = 0
		
		order by
			cl.name
			
			
		");
}
?>