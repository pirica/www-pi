<?php

if($id_queue > 0 && $filename != '')
{
	mysqli_query($conn, "
		
		update t_queue
		set
			filename = '" . mysqli_real_escape_string($conn, $filename) . "'
		where
			id_queue = " . $id_queue . "
			
		");
}

if($id_queue > 0 && $directory != '')
{
	mysqli_query($conn, "
		
		update t_queue
		set
			directory = '" . mysqli_real_escape_string($conn, $directory) . "'
		where
			id_queue = " . $id_queue . "
			
		");
}

if($id_queue > 0 && $playlistId >= 0)
{
	mysqli_query($conn, "
		
		update t_queue
		set
			playlistId = " . ($playlistId > 0 ? $playlistId : 'NULL') . "
		where
			id_queue = " . $id_queue . "
			
		");
}
	
?>