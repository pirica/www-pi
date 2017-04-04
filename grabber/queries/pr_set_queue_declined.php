<?php

mysqli_query($conn, "
	
	update t_queue
	set
		status = 'X'
	where
		id_queue = " . $id_queue . "
	
	");
	
?>