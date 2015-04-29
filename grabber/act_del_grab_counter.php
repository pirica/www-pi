<?php

$id_grab_counter = saneInput('id_grab_counter', 'int', -1);

if($id_grab_counter > 0){
	mysql_query("
		update t_grab_counter
		set
			active = 0
		where
			id_grab_counter = " . $id_grab_counter . "
		", $conn);
}

?>