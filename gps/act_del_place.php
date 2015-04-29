<?php
$id_place = saneInput('id_place', 'int', -1);

if($id_place > 0){
	mysql_query("
		update t_place
		set
			active = 0
		where
			id_place = " . $id_place . "
		", $conn);
}

?>