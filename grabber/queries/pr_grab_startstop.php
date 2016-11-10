<?php

mysqli_query($conn, "
	
	update t_grab
	set
		enabled = " . $grab_enabled . ",
		date_modified = '" . date('Y-m-d H:i:s', time()) . "'
	where
		id_grab = " . $id_grab . "
		and id_user = " . $_SESSION['user_id'] . "
	
	");
	
?>