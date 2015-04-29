<?php

mysql_query("
	
	update t_grab
	set
		enabled = " . $grab_enabled . ",
		date_modified = '" . date('Y-m-d H:i:s', time()) . "'
	where
		id_grab = " . $id_grab . "
	
	", $conn);
	
?>