<?php

mysqli_query($conn, "
	
	update t_directory
	set
		date_last_checked = null
	
	where
		id_share = " . $id_share . "
		and relative_directory = '" . $dir . "'
		
	");
	
?>