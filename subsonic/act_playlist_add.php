<?php

$error = 0;
$playlist_description = saneInput('playlist_description');

if($playlist_description != ''){
	
	include 'act_init_subsonic.php';
	
	$subsonic->createPlaylist($playlist_description);
	
	$playlists = $subsonic->getPlaylists();
	$c_playlists = count($playlists);
	
	for($pi=0; $pi<$c_playlists; $pi++){
		mysqli_query($conn, "
			replace into playlists
			(
				id,
				name,
				comment,
				owner,
				public,
				songcount,
				duration,
				created,
				active
			)
			values 
			(
				" . $playlists[$pi]->id . ",
				'" . mysqli_real_escape_string($conn, $playlists[$pi]->name) . "',
				'" . mysqli_real_escape_string($conn, property_exists($playlists[$pi], 'comment') ? $playlists[$pi]->comment : '') . "',
				'" . mysqli_real_escape_string($conn, property_exists($playlists[$pi], 'owner') ? $playlists[$pi]->owner : '') . "',
				" . ($playlists[$pi]->public == '' ? 0 : $playlists[$pi]->public) . ",
				" . $playlists[$pi]->songCount . ",
				" . $playlists[$pi]->duration . ",
				'" . mysqli_real_escape_string($conn, $playlists[$pi]->created) . "',
				1
			)
			");
			
	}
	
	goto_action('playlists');
}

?>