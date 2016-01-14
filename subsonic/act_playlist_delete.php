<?php

$error = 0;

if($playlistId > 0){
	
	$subsonic->deletePlaylist($playlistId);
	
	mysql_query("
		delete from playlistEntries
		where
			playlistId = " . $playlistId . "
			;
		");
	
	mysql_query("
		delete from playlists
		where
			id = " . $playlistId . "
			;
		");
	
	goto_action('playlists');
}

?>