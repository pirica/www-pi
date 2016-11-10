<?php

$error = 0;

if($playlistId > 0){
	
	include 'act_init_subsonic.php';
	
	$subsonic->deletePlaylist($playlistId);
	
	mysqli_query($conn, "
		delete from playlistEntries
		where
			playlistId = " . $playlistId . "
			;
		");
	
	mysqli_query($conn, "
		delete from playlists
		where
			id = " . $playlistId . "
			;
		");
	
	goto_action('playlists');
}

?>