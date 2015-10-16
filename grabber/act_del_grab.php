<?php
//$id_grab = -1;

if($id_grab > 0){
	mysql_query("
		update t_grab
		set
			active = 0
		where
			id_grab = " . $id_grab . "
			and id_user = " . $_SESSION['user_id'] . "
		", $conn);
}

?>