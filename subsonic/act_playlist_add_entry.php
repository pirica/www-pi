<?php

$error = 0;

if($playlistId > 0 && $songId != ''){
	
	/*
	include 'act_init_subsonic.php';
	
	$songIds = explode(',', $songId);
	for($i=0; $i<count($songIds); $i++){
		$subsonic->updatePlaylistAdd($playlistId, $songIds[$i]);
	}
	
	mysqli_query($conn, "delete from playlistEntries where playlistId = " . $playlistId);
	
	$playlist_entries = $subsonic->getPlaylist( $playlistId );
	$c_playlist_entries = count($playlist_entries);
	
	for($pei=0; $pei<$c_playlist_entries; $pei++){
		mysqli_query($conn, "
			insert into playlistEntries
			(
				playlistId,
				songId,
				songIndex
			)
			values 
			(
				" . $playlistId . ",
				" . $playlist_entries[$pei]->id . ",
				" . $pei . "
			)
			");
	}
	*/
	
	$songIds = explode(',', $songId);
	
	for($i=0; $i<count($songIds); $i++){
		mysqli_query($conn, "
			insert into playlistEntriesToAdd
			(
				playlistId,
				songId
			)
			values 
			(
				" . $playlistId . ",
				" . $songIds[$i] . "
			)
			");
	}
	
	
	//goto_action('playlists');
}

?>