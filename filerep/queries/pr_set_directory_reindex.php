<?php

mysql_query("
	
	update t_directory
	set
		date_last_checked = null
	
	where
		d.id_share = " . $id_share . "
		and d.relative_directory = '" . $dir . "'
		
	", $conn);
	
?>