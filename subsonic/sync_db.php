<?php

$crondate = time();

set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connection.php';
require dirname(__FILE__).'/../_core/components/subsonic/Api.php';

$s = new Subsonic(
	$settings->val('subsonic_server_username', 'admin'),
	$settings->val('subsonic_server_password', 'password'),
	$settings->val('subsonic_server_url', 'localhost'),
	$settings->val('subsonic_server_port', 4040),
	$settings->val('subsonic_clientname', 'subsonic_php')
);


$playlists = $s->getPlaylists();
$c_playlists = count($playlists);

if($c_playlists > 0){
	mysql_query("delete from playlistEntries;");
	mysql_query("truncate table playlistEntries;");
	mysql_query("truncate table playlists;");
	
	for($pi=0; $pi<$c_playlists; $pi++){
		mysql_query("
			insert into playlists
			(
				id,
				name,
				comment,
				owner,
				public,
				songcount,
				duration,
				created
			)
			values 
			(
				" . $playlists[$pi]->id . ",
				'" . $playlists[$pi]->name . "',
				'" . $playlists[$pi]->comment . "',
				'" . $playlists[$pi]->owner . "',
				" . $playlists[$pi]->public . ",
				" . $playlists[$pi]->songCount . ",
				" . $playlists[$pi]->duration . ",
				'" . $playlists[$pi]->created . "'
			)
			");
			
		
		$playlist_entries = $s->getPlaylist( $playlists[$pi]->id );
		$c_playlist_entries = count($playlist_entries);
		
		for($pei=0; $pei<$c_playlist_entries; $pei++){
			mysql_query("
				insert into playlistEntries
				(
					playlistId,
					songId,
					songIndex
				)
				values 
				(
					" . $playlists[$pi]->id . ",
					" . $playlist_entries[$pei]->id . ",
					" . $pei . "
				)
				");
		}
	}
}


$qry_indexes = mysql_query("select count(*) as indexcount from indexes;");
$indexes = mysql_fetch_array($qry_indexes);

if($indexes['indexcount'] == 0 || (date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5)){

	mysql_query("truncate table indexes;");
	mysql_query("truncate table songs;");

	$indexes = $s->getIndexes();
	$c_indexes = count($indexes);

	for($ii=0; $ii<$c_indexes; $ii++){
		$indexes_artists = $indexes[$ii]->artist;
		$c_indexes_artists = count($indexes_artists);
		
		
		for($iai=0; $iai<$c_indexes_artists; $iai++){
			mysql_query("
				insert into indexes
				(
					id,
					parentId,
					name
				)
				values 
				(
					" . $indexes_artists[$iai]->id . ",
					0,
					'" . $indexes_artists[$iai]->name . "'
				)
				");
			
		}
	}


	$rowcount = 1;
	while($rowcount > 0){
		$qry_indexes = mysql_query("
			select
				id
			from indexes
			where
				checked = 0
			");
			
		$rowcount = mysql_affected_rows($conn);
		
		mysql_query("
			update indexes
			set
				checked = 1
			where
				checked = 0
			");
			
		while($index = mysql_fetch_array($qry_indexes)){
			
			$music_directories = $s->getMusicDirectory($index['id']);
			$c_music_directories = count($music_directories);

			for($mdi=0; $mdi<$c_music_directories; $mdi++){
				
				if($music_directories[$mdi]->isDir){
					mysql_query("
						insert into indexes
						(
							id,
							parentId,
							name
						)
						values 
						(
							" . $music_directories[$mdi]->id . ",
							" . $music_directories[$mdi]->parent . ",
							'" . $music_directories[$mdi]->title . "'
						)
						");
				}
				else {
					$paths = explode('/', $music_directories[$mdi]->path);
					$filename = array_pop($paths);
					$relative_directory = '/' . implode('/', $paths) . '/';
					
					mysql_query("
						insert into songs
						(
							id,
							parentId,
							title,
							album,
							artist,
							track,
							year,
							genre,
							size,
							contentType,
							suffix,
							duration,
							bitRate,
							path,
							filename,
							relative_directory,
							isVideo,
							created,
							type,
							albumId
						)
						values 
						(
							" . $music_directories[$mdi]->id . ",
							" . $music_directories[$mdi]->parent . ",
							'" . (property_exists($music_directories[$mdi], 'title') ? $music_directories[$mdi]->title : '') . "',
							'" . (property_exists($music_directories[$mdi], 'album') ? $music_directories[$mdi]->album : '') . "',
							'" . (property_exists($music_directories[$mdi], 'artist') ? $music_directories[$mdi]->artist : '') . "',
							" . (property_exists($music_directories[$mdi], 'track') ? $music_directories[$mdi]->track : '-1') . ",
							" . (property_exists($music_directories[$mdi], 'year') ? $music_directories[$mdi]->year : '-1') . ",
							'" . (property_exists($music_directories[$mdi], 'genre') ? $music_directories[$mdi]->genre : '') . "',
							" . (property_exists($music_directories[$mdi], 'title') ? $music_directories[$mdi]->size : '-1') . ",
							'" . $music_directories[$mdi]->contentType . "',
							'" . $music_directories[$mdi]->suffix . "',
							" . (property_exists($music_directories[$mdi], 'duration') ? $music_directories[$mdi]->duration : '-1') . ",
							" . (property_exists($music_directories[$mdi], 'bitRate') ? $music_directories[$mdi]->bitRate : '-1') . ",
							'" . $music_directories[$mdi]->path . "',
							'" . $filename . "',
							'" . $relative_directory . "',
							" . ($music_directories[$mdi]->isVideo ? 1 : 0) . ",
							'" . $music_directories[$mdi]->created . "',
							'" . $music_directories[$mdi]->type . "',
							" . (property_exists($music_directories[$mdi], 'albumId') ? $music_directories[$mdi]->albumId: '-1') . "
						)
						");
						
				}
			}
			
		}
	}

}

// update filerep
mysql_query("
	update filerep.t_file
	set
		ss_on_playlist = 0
	where
		ss_on_playlist = 1
	;
		
	update filerep.t_file f
	join songs s on f.relative_directory = s.relative_directory and f.filename = s.filename
	join playlistEntries p on p.songid = s.id
	set
		f.ss_on_playlist = 1
	;
	", $conn);

?>