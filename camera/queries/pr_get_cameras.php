<?php
	
$qry_cameras = mysql_query("
	
	select
		c.id_camera,
		c.address,
        c.address_fallback,
        c.type,
        c.description,
        c.is_local
		
	from t_camera c
	where
		c.is_local = " . $_SESSION['local'] . "
	
	order by
		c.description
		
		
	");
	
?>