<?php
	
$qry_camera_log = mysqli_query($conn, "
	
	select
		cl.id_camera_log_archive,
		cl.date,
		cl.time,
		cl.hour_lbl,
		cl.time_value,
		cl.name,
		cl.camera
		
	from t_camera_log_archive cl
	where
		cl.date = '" . $date . "'
		and cl.hour_lbl = ifnull(" . ($time == 'all' ? 'null' : "'".$time."'") . ", cl.hour_lbl)
		and ifnull(status,0) = 0
		" . ($camera == '' ? '' : "and cl.camera = '" . mysqli_real_escape_string($conn, $camera) . "'") . "
	
	order by
		cl.hour_lbl,
		case when cl.name like '%.mp4' or cl.name like '%.avi' then 0 else 1 end,
		cl.name
		
		
	");
	
	$qry_cameras_logged = mysqli_query($conn, "
		
		select distinct
			cl.camera
			
		from t_camera_log_archive cl
		where
			cl.date = '" . $date . "'
			and cl.hour_lbl = ifnull(" . ($time == 'all' ? 'null' : "'".$time."'") . ", cl.hour_lbl)
			and ifnull(status,0) = 0
		
		order by
			cl.camera
			
		");
?>