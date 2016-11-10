<?php
	
$qry_camera_log_menu = mysqli_query($conn, "
	
	select
		cl.date,
		cl.hour_lbl,
		sum(case when cl.name like '%.jpg' then 1 else 0 end) as nbr_images,
		sum(case when cl.name like '%.mp4' or cl.name like '%.avi' then 1 else 0 end) as nbr_videos
		
	from t_camera_log_archive cl
	where
		ifnull(status,0) = 0
	
	group by
		cl.date,
		cl.hour_lbl
		
	order by
		cl.date,
		cl.hour_lbl
		
		
	");
	
$camera_log_menu_data = [];

while($camera_log_menu = mysqli_fetch_array($qry_camera_log_menu)){
	$camera_log_menu_data[$camera_log_menu['date']][] = $camera_log_menu;
}
	
?>