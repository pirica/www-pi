<?php

$crondate = time();

set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connection.php';

include 'act_init_subsonic.php';


if(date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5){
	$qry_entries = mysql_query("
		select
			id,
			playlistId,
			songId
		from playlistEntriesToAdd
		");
	
	while($entry = mysql_fetch_array($qry_entries)){
		$subsonic->updatePlaylistAdd($entry['playlistId'], $entry['songId']);
		mysql_query("delete from playlistEntriesToAdd where id = " . $entry['id']);
	}
}

$playlists = $subsonic->getPlaylists();
$c_playlists = count($playlists);

if($c_playlists > 0){
	mysql_query("delete from playlistEntries");
	mysql_query("truncate table playlistEntries");
	//mysql_query("truncate table playlists");
	
	mysql_query("update playlists set active = 2");
	
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
				" . ($playlists[$pi]->public == '' ? 0 : $playlists[$pi]->public) . ",
				" . $playlists[$pi]->songCount . ",
				" . $playlists[$pi]->duration . ",
				'" . mysql_real_escape_string($playlists[$pi]->created) . "',
				1
			)
			");
			
		
		$playlist_entries = $subsonic->getPlaylist( $playlists[$pi]->id );
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
	
	mysql_query("update playlists set active = 0 where active = 2");
	
}

/*

if setting "remove double entries"
	
	query "get double entries"
	ordered by first or last, by index desc
		
		$subsonic->updatePlaylistRemove($playlistId, $playlistSongIndex);
		mysql_query("delete from playlistEntries where id = " . $entry['id']);
		

*/


$qry_indexes = mysql_query("select count(*) as indexcount from indexes");
$indexes = mysql_fetch_array($qry_indexes);

if($indexes['indexcount'] == 0 || (date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5)){

	mysql_query("truncate table indexes");
	//mysql_query("truncate table songs");
	
	mysql_query("update songs set active = 2");
	
	
	$indexes = $subsonic->getIndexes();
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
					'" . mysql_real_escape_string($indexes_artists[$iai]->name) . "'
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
			
			$music_directories = $subsonic->getMusicDirectory($index['id']);
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
							'" . mysql_real_escape_string($music_directories[$mdi]->title) . "'
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
							albumId,
							active
						)
						select
							" . $music_directories[$mdi]->id . ",
							" . $music_directories[$mdi]->parent . ",
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'title') ? $music_directories[$mdi]->title : '') . "',
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'album') ? $music_directories[$mdi]->album : '') . "',
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'artist') ? $music_directories[$mdi]->artist : '') . "',
							" . (property_exists($music_directories[$mdi], 'track') ? $music_directories[$mdi]->track : '-1') . ",
							" . (property_exists($music_directories[$mdi], 'year') ? $music_directories[$mdi]->year : '-1') . ",
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'genre') ? $music_directories[$mdi]->genre : '') . "',
							" . (property_exists($music_directories[$mdi], 'size') ? $music_directories[$mdi]->size : '-1') . ",
							'" . mysql_real_escape_string($music_directories[$mdi]->contentType) . "',
							'" . mysql_real_escape_string($music_directories[$mdi]->suffix) . "',
							" . (property_exists($music_directories[$mdi], 'duration') ? $music_directories[$mdi]->duration : '-1') . ",
							" . (property_exists($music_directories[$mdi], 'bitRate') ? $music_directories[$mdi]->bitRate : '-1') . ",
							'" . mysql_real_escape_string($music_directories[$mdi]->path) . "',
							'" . mysql_real_escape_string($filename) . "',
							'" . mysql_real_escape_string($relative_directory) . "',
							" . ($music_directories[$mdi]->isVideo ? 1 : 0) . ",
							'" . mysql_real_escape_string($music_directories[$mdi]->created) . "',
							'" . mysql_real_escape_string($music_directories[$mdi]->type) . "',
							" . (property_exists($music_directories[$mdi], 'albumId') ? $music_directories[$mdi]->albumId: '-1') . ",
							1
						from songs s1
						left join songs s2 on s2.id = " . $music_directories[$mdi]->id . "
						where s2.id is null
						limit 1,1
						
						");
						
					mysql_query("
						replace into songs
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
							albumId,
							active,
							
							mainGenreId,
							export,
							title_custom,
							artist_custom
						)
						select
							" . $music_directories[$mdi]->id . ",
							" . $music_directories[$mdi]->parent . ",
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'title') ? $music_directories[$mdi]->title : '') . "',
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'album') ? $music_directories[$mdi]->album : '') . "',
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'artist') ? $music_directories[$mdi]->artist : '') . "',
							" . (property_exists($music_directories[$mdi], 'track') ? $music_directories[$mdi]->track : '-1') . ",
							" . (property_exists($music_directories[$mdi], 'year') ? $music_directories[$mdi]->year : '-1') . ",
							'" . mysql_real_escape_string(property_exists($music_directories[$mdi], 'genre') ? $music_directories[$mdi]->genre : '') . "',
							" . (property_exists($music_directories[$mdi], 'size') ? $music_directories[$mdi]->size : '-1') . ",
							'" . mysql_real_escape_string($music_directories[$mdi]->contentType) . "',
							'" . mysql_real_escape_string($music_directories[$mdi]->suffix) . "',
							" . (property_exists($music_directories[$mdi], 'duration') ? $music_directories[$mdi]->duration : '-1') . ",
							" . (property_exists($music_directories[$mdi], 'bitRate') ? $music_directories[$mdi]->bitRate : '-1') . ",
							'" . mysql_real_escape_string($music_directories[$mdi]->path) . "',
							'" . mysql_real_escape_string($filename) . "',
							'" . mysql_real_escape_string($relative_directory) . "',
							" . ($music_directories[$mdi]->isVideo ? 1 : 0) . ",
							'" . mysql_real_escape_string($music_directories[$mdi]->created) . "',
							'" . mysql_real_escape_string($music_directories[$mdi]->type) . "',
							" . (property_exists($music_directories[$mdi], 'albumId') ? $music_directories[$mdi]->albumId: '-1') . ",
							1,
							
							mainGenreId,
							export,
							title_custom,
							artist_custom
							
						from songs 
						where id = " . $music_directories[$mdi]->id . "
						
						");
						
				}
			}
			
		}
	}
	
	mysql_query("update songs set active = 0 where active = 2");
	
	mysql_query("
		update songs 
		set
			artist_custom = replace(
				LEFT(filename, INSTR(replace(filename,'_', ' ')," - ")-1)
			,'_', ' ')  
		where 
			type = 'music' 
			and active = 1 
			and replace(filename,'_', ' ') like '% - %' 
			#and ifnull(artist_custom,'') = '' 
	");
	
	mysql_query("
		update songs 
		set
			artist_custom = replace(
				substring(artist_custom, INSTR(artist_custom,". ") + 2)
			,'_', ' ')  
		where 
			type = 'music' 
			and active = 1 
			and artist_custom like '%. %'
			and SUBSTRING(artist_custom, 1, INSTR(artist_custom,". ")) REGEXP '[[:digit:]]'
	");
	
	// insert/update artists
	mysql_query("
		insert into artists (description, songs)
		select artist_custom, count(s.id) from songs s
		left join artists a on a.description = s.artist_custom
		where s.artist_custom <> ''
		and a.id is null
		group by s.artist_custom
	");
	
	mysql_query("
		update artists a
		join songs s on a.description = s.artist_custom
		set
			a.songs = count(s.id)
		group by s.artist_custom
		");
	
	mysql_query("
		update artists a
		left join songs s on a.description = s.artist_custom
		set
			a.active = 0
		where
			s.id is null
		");
		
	// select filename, replace(LEFT(filename, INSTR(replace(filename,'_', ' ')," - ")-1) ,'_', ' ') as artist from songs where type = 'music' and active = 1 and replace(filename,'_', ' ') like '% - %'  order by 1
	// select  artist_custom, count(id) from songs where type = 'music' and active = 1 and artist_custom <> '' group by  artist_custom
	
	mysql_query("
		insert into genres (description)
		select distinct genre from songs s
		left join genres g on g.description = s.genre
		where s.genre <> ''
		and g.id is null
	");
	
}



/*
$qry_users = mysql_query("select count(*) as usercount from users where active = 1");
$users = mysql_fetch_array($qry_users);

if($users['usercount'] == 0 || (date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5)){
*/
	$users = $subsonic->getUsers();
	$c_users = count($users);
	$usernames = '';

	for($ui=0; $ui<$c_users; $ui++){
		$usernames .= ($usernames == '' ? '' : ',') . "'" . mysql_real_escape_string($users[$ui]->username) . "'"; 
		
		mysql_query("
			replace into users
			(
				username,
				active,
				
				email
			)
			values 
			(
				'" . mysql_real_escape_string($users[$ui]->username) . "',
				1,
				
				'" . (property_exists($users[$ui], 'email') ? mysql_real_escape_string($users[$ui]->email) : '') . "'
			)
			");
			
	}

	mysql_query("update users set active = 0 where username not in (" . $usernames . ") and active = 1");

//}


/*
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
*/


$export_dir = $settings->val('export_directory', '');
$songs_dir = $settings->val('songs_directory', '');

$is_dir = $export_dir != '';
try {
	$is_dir = is_dir($export_dir);
}
catch(Exception $e){}

if($is_dir && substr($export_dir, -1, 1) != '/'){
	$export_dir = $export_dir . '/';
}
if($songs_dir != '' && substr($songs_dir, -1, 1) != '/'){
	$songs_dir = $songs_dir . '/';
}

if($settings->val('export_songs', 0) == 1 && $is_dir)
{
	
	// check and create genre dirs
	$qry_genres = mysql_query("
		select distinct
			mg.description as genre
		
		from songs s
			left join genres g on g.description = s.genre and s.genre <> ''
			join mainGenres mg on mg.id = ifnull(s.maingenreid, g.maingenreid)
		
		where
			ifnull(s.export,0) <> 0
			or s.active = 0
		");
		
	while($genre = mysql_fetch_array($qry_genres)){
		/*
		if active = 1 and export = 1
			if file not exists
				if file.deleted exists
					delete? + unflag
				else
					copy
		
		else
			if file exists
				delete + unflag
		*/
		
		$genre_dir = $export_dir . $genre['genre'];
		$is_genre_dir = false;
		try {
			$is_genre_dir = is_dir($genre_dir);
		}
		catch(Exception $e){}
		
		if($is_genre_dir !== true)
		{
			mkdir($genre_dir);
		}
		shell_exec ('sudo chown nobody:nogroup -R "' . $genre_dir . '"');
		shell_exec ('sudo chmod 777 -R "' . $genre_dir . '"');
	}
		
	// get songs to export (export = 1) and to remove from export (export = -1 or active = 0)
	$qry_songs = mysql_query("
		select
			s.id,
			s.path,
			s.filename,
			s.relative_directory,
			s.export,
			s.active,
			mg.description as genre
		
		from songs s
			left join genres g on g.description = s.genre and s.genre <> ''
			join mainGenres mg on mg.id = ifnull(s.maingenreid, g.maingenreid)
		
		where
			ifnull(s.export,0) <> 0
			or s.active = 0
		");
		
	while($song = mysql_fetch_array($qry_songs)){
		/*
		if active = 1 and export = 1
			if file not exists
				if file.deleted exists
					delete? + unflag
				else
					copy
		
		else
			if file exists
				delete + unflag
		*/
		
		$genre_dir = $export_dir . $song['genre'];
		
		if($song['active'] == 1 && $song['export'] == 1)
		{
			if(!file_exists($genre_dir . '/' . $song['filename']))
			{
				if(file_exists($genre_dir. '/' . $song['filename'] . '.deleted'))
				{
					unlink($genre_dir . '/' . $song['filename'] . '.deleted');
				}
				else
				{
					copy($songs_dir . $song['path'], $genre_dir . '/' . $song['filename']);
				}
			}
		}
		else 
		{
			if(file_exists($genre_dir . '/' . $song['filename']))
			{
				unlink($genre_dir . '/' . $song['filename']);
			}
			else if(file_exists($genre_dir. '/' . $song['filename'] . '.deleted'))
			{
				unlink($genre_dir . '/' . $song['filename'] . '.deleted');
			}
		}
		
	}
	
	// unflag deleted
	mysql_query("
		update songs
		set
			export = 0
		where
			export = -1
			or active = 0
		");
	
}


?>