<?php

if($id_queue > 0 && $field != '' && $value != '')
{
	$field = str_replace("'", '', $field);
	$field = str_replace(" ", '', $field);
	$field = str_replace("-", '', $field);
	$field = str_replace(";", '', $field);
	$field = str_replace("=", '', $field);
	
	mysqli_query($conn, "
		
		update t_queue
		set
			" . $field . " = '" . mysqli_real_escape_string($conn, $value) . "'
		where
			id_queue = " . $id_queue . "
			
		");
}

?>