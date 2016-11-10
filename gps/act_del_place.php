<?php
$id_place = saneInput('id_place', 'int', -1);

if($id_place > 0){
	mysqli_query($conn, "
		update t_place
		set
			active = 0
		where
			id_place = " . $id_place . "
		");
}

?>