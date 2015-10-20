<?php
	
$qry_camera_log_menu = mysql_query("
	
	select
		cm.date,
		cm.hour_lbl,
		cm.nbr_images,
		cm.nbr_videos
		
	from t_camera_menu cm
	
	order by
		cm.date_hour_lbl desc
		
		
	");
	
$camera_log_menu_data = [];

while($camera_log_menu = mysql_fetch_array($qry_camera_log_menu)){
	$camera_log_menu_data[$camera_log_menu['date']][] = $camera_log_menu;
}
	
?>