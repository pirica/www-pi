<?php
	
$qry_tracks = mysqli_query($conn, "
	
	select
		t.id_track,
		t.description,
		t.pre_description,
		t.lat_top,
		t.lon_right,
		t.lat_bottom,
		t.lon_left
		
	from t_track t
	where
		t.active = 1
	
	order by
		t.description
		
		
	");
	
?>