<?php

$error = 0;
$description = saneInput('description');

if($description != ''){
	
	include 'act_init_subsonic.php';
	
	$subsonic->createPlaylist($description);
	
	$playlists = $subsonic->getPlaylists();
	$c_playlists = count($playlists);
	
	for($pi=0; $pi<$c_playlists; $pi++){
		mysql_query("
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
				'" . mysql_real_escape_string($playlists[$pi]->name) . "',
				'" . mysql_real_escape_string(property_exists($playlists[$pi], 'comment') ? $playlists[$pi]->comment : '') . "',
				'" . mysql_real_escape_string(property_exists($playlists[$pi], 'owner') ? $playlists[$pi]->owner : '') . "',
				" . $playlists[$pi]->public . ",
				" . $playlists[$pi]->songCount . ",
				" . $playlists[$pi]->duration . ",
				'" . mysql_real_escape_string($playlists[$pi]->created) . "',
				1
			)
			");
			
	}
	
	goto_action('playlists');
}

?>