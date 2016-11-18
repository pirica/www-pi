<?php

mysqli_query($conn, "
	
	update t_file
	set
		rename_to = '" . mysqli_real_escape_string($conn, $rename_to) . "'
	
	where
		id_file = " . $id_file . "
		and id_share = " . $id_share . "
		
	");
	
mysqli_query($conn, "
	
	update t_directory
	set
		date_last_checked = null
	
	where
		id_share = " . $id_share . "
		and relative_directory in (
			select relative_directory
			from t_file
			where
				id_file = " . $id_file . "
				and id_share = " . $id_share . "
		)
		
	");
	
	
?>