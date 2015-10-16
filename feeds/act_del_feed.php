<?php
//$id_feed = -1;

if($id_feed > 0){
	mysql_query("
		update t_feed
		set
			active = 0
		where
			id_feed = " . $id_feed . "
			and id_user = " . $_SESSION['user_id'] . "
		", $conn);
}

?>