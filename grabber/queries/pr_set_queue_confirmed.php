<?php

mysqli_query($conn, "
	
	update t_queue
	set
		status = 'V'
	where
		id_queue = " . $id_queue . "
		and status = 'Y'
	
	");
	
mysqli_query($conn, "
	
	update t_queue
	set
		status = 'D'
	where
		id_queue = " . $id_queue . "
		and status = 'F
		'
	
	");
	
?>