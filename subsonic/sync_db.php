<?php

$crondate = time();

set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connection.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);

	include 'act_init_subsonic.php';


	//if(date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5)
	{
		
		$qry_entries = mysqli_query($conn, "
			select
				per.id,
				per.playlistId,
				per.songId,
				pe.songIndex
			from playlistEntriesToRemove per
			join playlistEntries pe on pe.playlistId = per.playlistId  and pe.songId = per.songId
			order by
				pe.songIndex desc
			");
		
		while($entry = mysqli_fetch_array($qry_entries)){
			$subsonic->updatePlaylistRemove($entry['playlistId'], $entry['songIndex']);
			mysqli_query($conn, "delete from playlistEntriesToRemove where id = " . $entry['id']);
		}
		
		
		$qry_entries = mysqli_query($conn, "
			select
				id,
				playlistId,
				songId
			from playlistEntriesToAdd
			order by id
			");
		
		while($entry = mysqli_fetch_array($qry_entries)){
			$subsonic->updatePlaylistAdd($entry['playlistId'], $entry['songId']);
			mysqli_query($conn, "delete from playlistEntriesToAdd where id = " . $entry['id']);
		}
	}


	$qry_indexes = mysqli_query($conn, "select count(*) as indexcount from indexes");
	$indexes = mysqli_fetch_array($qry_indexes);

	//if($indexes['indexcount'] == 0 || (date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5))
	{

		mysqli_query($conn, "truncate table indexes");
		mysqli_query($conn, "truncate table sync_songs");
		
		mysqli_query($conn, "update songs set active = 2 where active = 1");
		
		
		$indexes = $subsonic->getIndexes();
		$c_indexes = count($indexes);

		for($ii=0; $ii<$c_indexes; $ii++){
			$indexes_artists = $indexes[$ii]->artist;
			$c_indexes_artists = count($indexes_artists);
			
			
			for($iai=0; $iai<$c_indexes_artists; $iai++){
				mysqli_query($conn, "
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
						'" . mysqli_real_escape_string($conn, $indexes_artists[$iai]->name) . "'
					)
					");
				
			}
		}


		$rowcount = 1;
		while($rowcount > 0){
			$qry_indexes = mysqli_query($conn, "
				select
					id
				from indexes
				where
					checked = 0
				");
				
			$rowcount = mysqli_affected_rows($conn);
			
			mysqli_query($conn, "
				update indexes
				set
					checked = 1
				where
					checked = 0
				");
				
			while($index = mysqli_fetch_array($qry_indexes)){
				
				$music_directories = $subsonic->getMusicDirectory($index['id']);
				$c_music_directories = count($music_directories);

				for($mdi=0; $mdi<$c_music_directories; $mdi++){
					
					if($music_directories[$mdi]->isDir){
						mysqli_query($conn, "
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
								'" . mysqli_real_escape_string($conn, $music_directories[$mdi]->title) . "'
							)
							");
					}
					else {
						$paths = explode('/', $music_directories[$mdi]->path);
						$filename = array_pop($paths);
						$relative_directory = '/' . implode('/', $paths) . '/';
						
						mysqli_query($conn, "
							insert into sync_songs
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
								'" . mysqli_real_escape_string($conn, property_exists($music_directories[$mdi], 'title') ? $music_directories[$mdi]->title : '') . "',
								'" . mysqli_real_escape_string($conn, property_exists($music_directories[$mdi], 'album') ? $music_directories[$mdi]->album : '') . "',
								'" . mysqli_real_escape_string($conn, property_exists($music_directories[$mdi], 'artist') ? $music_directories[$mdi]->artist : '') . "',
								" . (property_exists($music_directories[$mdi], 'track') ? $music_directories[$mdi]->track : '-1') . ",
								" . (property_exists($music_directories[$mdi], 'year') ? $music_directories[$mdi]->year : '-1') . ",
								'" . mysqli_real_escape_string($conn, property_exists($music_directories[$mdi], 'genre') ? $music_directories[$mdi]->genre : '') . "',
								" . (property_exists($music_directories[$mdi], 'size') ? $music_directories[$mdi]->size : '-1') . ",
								'" . mysqli_real_escape_string($conn, $music_directories[$mdi]->contentType) . "',
								'" . mysqli_real_escape_string($conn, $music_directories[$mdi]->suffix) . "',
								" . (property_exists($music_directories[$mdi], 'duration') ? $music_directories[$mdi]->duration : '-1') . ",
								" . (property_exists($music_directories[$mdi], 'bitRate') ? $music_directories[$mdi]->bitRate : '-1') . ",
								'" . mysqli_real_escape_string($conn, $music_directories[$mdi]->path) . "',
								'" . mysqli_real_escape_string($conn, $filename) . "',
								'" . mysqli_real_escape_string($conn, $relative_directory) . "',
								" . 0 /*($music_directories[$mdi]->isVideo ? 1 : 0)*/ . ",
								" . 'NULL' /*"'".mysqli_real_escape_string($conn, $music_directories[$mdi]->created)."'"*/ . ",
								'" . /*mysqli_real_escape_string($conn, $music_directories[$mdi]->type) .*/ "',
								" . (property_exists($music_directories[$mdi], 'albumId') ? $music_directories[$mdi]->albumId: '-1') . "
							)
							");
						
					}
				}
				
			}
		}
		
		mysqli_query($conn, "
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
				active,
				newlyImported
			)
			select
				ss.id,
				ss.parentId,
				ss.title,
				ss.album,
				ss.artist,
				ss.track,
				ss.year,
				ss.genre,
				ss.size,
				ss.contentType,
				ss.suffix,
				ss.duration,
				ss.bitRate,
				ss.path,
				ss.filename,
				ss.relative_directory,
				ss.isVideo,
				ss.created,
				ss.type,
				ss.albumId,
				1,
				1
			from sync_songs ss
			left join songs s on s.id = ss.id
			where
				s.id is null
		");
		
		mysqli_query($conn, "
			update songs s
			join sync_songs ss on ss.id = s.id
			set
				s.parentId = ss.parentId,
				s.title = ss.title,
				s.album = ss.album,
				s.artist = ss.artist,
				s.track = ss.track,
				s.year = ss.year,
				s.genre = ss.genre,
				s.size = ss.size,
				s.contentType = ss.contentType,
				s.suffix = ss.suffix,
				s.duration = ss.duration,
				s.bitRate = ss.bitRate,
				s.path = ss.path,
				s.filename = ss.filename,
				s.relative_directory = ss.relative_directory,
				s.isVideo = ss.isVideo,
				s.created = ss.created,
				s.type = ss.type,
				s.albumId = ss.albumId,
				s.active = 1
			
		");
		
		mysqli_query($conn, "update songs set active = 0 where active = 2");
		
		mysqli_query($conn, "
			update songs 
			set
				artist_custom = replace(
					LEFT(filename, INSTR(replace(filename,'_', ' ')," - ")-1)
				,'_', ' ')  
			where 
				contentType like 'audio/%' 
				and active = 1 
				and replace(filename,'_', ' ') like '% - %' 
				#and ifnull(artist_custom,'') = '' 
		");
		
		mysqli_query($conn, "
			update songs 
			set
				  = replace(
					substring(artist_custom, INSTR(artist_custom,". ") + 2)
				,'_', ' ')  
			where 
				contentType like 'audio/%' 
				and active = 1 
				and artist_custom like '%. %'
				and SUBSTRING(artist_custom, 1, INSTR(artist_custom,". ")) REGEXP '[[:digit:]]'
		");
		
		mysqli_query($conn, "
			UPDATE songs SET artist_custom = LOWER(artist_custom);
			UPDATE songs SET artist_custom = CONCAT(UPPER(SUBSTR(artist_custom,1,1)),LOWER(SUBSTR(artist_custom,2)));
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' a',' A');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' b',' B');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' c',' C');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' d',' D');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' e',' E');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' f',' F');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' g',' G');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' h',' H');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' i',' I');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' j',' J');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' k',' K');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' l',' L');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' m',' M');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' n',' N');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' o',' O');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' p',' P');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' q',' Q');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' r',' R');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' s',' S');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' t',' T');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' u',' U');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' v',' V');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' w',' W');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' x',' X');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' y',' Y');
			UPDATE songs SET artist_custom = REPLACE(artist_custom,' z',' Z');
		");
		
		// insert/update artists
		mysqli_query($conn, "
			insert into artists (description, songs)
			select artist_custom, count(s.id) from songs s
			left join artists a on concat(ifnull(concat(nullif(a.prefix, ''), ' '), ''), a.description) = s.artist_custom
			where s.active = 1 
			and s.artist_custom <> ''
			and a.id is null
			group by s.artist_custom
		");
		
		$articles = 'The El La Los Las Le Les De Het Dj';
		//$articles .= ' ' . strtoupper($articles);
		//$articles .= ' ' . strtolower($articles);
		$a_articles = explode(' ', $articles);
		for($i=0; $i<count($a_articles); $i++)
		{
			// fix artist prefixes
			mysqli_query($conn, "
				update artists
				set
					prefix = '" . $a_articles[$i] . "',
					description = replace(description, '" . $a_articles[$i] . " ', '')
				where 
					prefix = ''
					and left(description, " . (strlen($a_articles[$i]) + 1) . ") = '" . $a_articles[$i] . " '
			");
		}
		
		// update nbr of songs per artist
		mysqli_query($conn, "update artists set songs = 0");
		mysqli_query($conn, "
			update artists
			set
				songs = (
					select count(id) 
					from songs
					where songs.artist_custom = concat(ifnull(concat(nullif(artists.prefix, ''), ' '), ''), artists.description)
					group by songs.artist_custom
				)
			");
		
		if($settings->val('auto_delete_artists', 0) == 1)
		{
			// reactivate artists with any song (by setting)
			mysqli_query($conn, "
				update artists
				set
					active = 1
				where
					active = 0
					and songs > 0
				");
				
			// delete artists without any song (by setting)
			mysqli_query($conn, "
				update artists
				set
					active = 0
				where
					active = 1
					and songs = 0
				");
		}
			
		// select filename, replace(LEFT(filename, INSTR(replace(filename,'_', ' ')," - ")-1) ,'_', ' ') as artist from songs where type = 'music' and active = 1 and replace(filename,'_', ' ') like '% - %'  order by 1
		// select  artist_custom, count(id) from songs where type = 'music' and active = 1 and artist_custom <> '' group by  artist_custom
		
		
		// insert/update genres
		mysqli_query($conn, "
			insert into genres (description)
			select distinct genre from songs s
			left join genres g on g.description = s.genre
			where s.genre <> ''
			and g.id is null
		");
		
		// update nbr of songs per genre
		mysqli_query($conn, "update genres set songs = 0");
		mysqli_query($conn, "
			update genres
			set
				songs = (
					select count(id) 
					from songs
					where songs.genre = genres.description
					group by songs.genre
				)
			");
			
		if($settings->val('auto_delete_genres', 0) == 1)
		{
			// reactivate genres with any song (by setting)
			mysqli_query($conn, "
				update genres
				set
					active = 1
				where
					active = 0
					and songs > 0
				");
				
			// delete genres without any song (by setting)
			mysqli_query($conn, "
				update genres
				set
					active = 0
				where
					active = 1
					and songs = 0
				");
		}
		
		if($settings->val('update_moved_playlistentries', 0) == 1)
		{
			// update songs from playlists which were moved
			mysqli_query($conn, "
				insert into playlistEntriesToAdd (playlistId, songId)
				select pe.playlistId, s2.id
				from playlistEntries pe
				join songs s on s.id = pe.songId and s.active = 0
				join songs s2 on s2.filename = s.filename and s2.active = 1
				left join playlistEntriesToAdd pea on pea.playlistId = pe.playlistId and pea.songId = s2.id
				where
					pea.id is null
			");
			
		}
		
		
		if($settings->val('auto_delete_inactive_playlistentries', 0) == 1)
		{
			// delete songs from playlists which are inactive
			mysqli_query($conn, "
				insert into playlistEntriesToRemove (playlistId, songId)
				select pe.playlistId, pe.songId
				from playlistEntries pe
				join songs s on s.id = pe.songId and s.active = 0
			");
		}
		
		
		// newly imported, add to 'intake' playlist
		if($settings->val('intake_playlist', -1) > 0)
		{
			mysqli_query($conn, "
				insert into playlistEntriesToAdd (playlistId, songId)
				select " . $settings->val('intake_playlist', -1) . ", s.id from songs s
				left join songs s2 on s2.filename = s.filename and s2.active = 0
				left join playlistEntriesToAdd pea on pea.playlistId = " . $settings->val('intake_playlist', -1) . " and pea.songId = s2.id
				where s.active = 1 
				and s.newlyImported = 1
				and s2.id is null
				and pea.id is null
			");
		}
		mysqli_query($conn, "update songs set newlyImported = 0 where newlyImported = 1");
		
	}



	/*
	$qry_users = mysqli_query($conn, "select count(*) as usercount from users where active = 1");
	$users = mysqli_fetch_array($qry_users);

	if($users['usercount'] == 0 || (date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5)){
	*/
		$users = $subsonic->getUsers();
		$c_users = count($users);
		$usernames = '';

		for($ui=0; $ui<$c_users; $ui++){
			$usernames .= ($usernames == '' ? '' : ',') . "'" . mysqli_real_escape_string($conn, $users[$ui]->username) . "'"; 
			
			mysqli_query($conn, "
				replace into users
				(
					username,
					active,
					
					email
				)
				values 
				(
					'" . mysqli_real_escape_string($conn, $users[$ui]->username) . "',
					1,
					
					'" . (property_exists($users[$ui], 'email') ? mysqli_real_escape_string($conn, $users[$ui]->email) : '') . "'
				)
				");
				
				/*
				"username" : "admin",
				"scrobblingEnabled" : false,
				"adminRole" : true,
				"settingsRole" : true,
				"downloadRole" : true,
				"uploadRole" : true,
				"playlistRole" : true,
				"coverArtRole" : true,
				"commentRole" : true,
				"podcastRole" : true,
				"streamRole" : true,
				"jukeboxRole" : true,
				"shareRole" : true,
				"videoConversionRole" : true,
				"avatarLastChanged" : "2016-11-23T13:47:55.841Z"
				*/
		}

		mysqli_query($conn, "update users set active = 0 where username not in (" . $usernames . ") and active = 1");

	//}

	
	
	$playlists = $subsonic->getPlaylists();
	$c_playlists = count($playlists);

	if($c_playlists > 0){
		
		mysqli_query($conn, "update playlists set active = 2");
		
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
				
			mysqli_query($conn, "
				SET @o='{\"options\": [{\"code\": \"-1\", \"value\": \"\"}';
				
				select
					@o := concat(@o, ',{\"code\": \"', id, '\", \"value\": \"', name, '\"}')
				from playlists
				where
					active = 1
				order by
					name
				;
				
				select @o := concat(@o, ']}');
				
				update users.t_setting
				set
					extra = @o
				where
					code = 'intake_playlist'
				;
				
				");
			
			
			$playlist_entries = $subsonic->getPlaylist( $playlists[$pi]->id );
			$c_playlist_entries = count($playlist_entries);
			
			for($pei=0; $pei<$c_playlist_entries; $pei++){
				mysqli_query($conn, "
					replace into playlistEntries
					(
						id,
						playlistId,
						songId,
						songIndex
					)
					values 
					(
						'" . $playlists[$pi]->id . '-' . $pei . "',
						" . $playlists[$pi]->id . ",
						" . $playlist_entries[$pei]->id . ",
						" . $pei . "
					)
					");
			}
			
			mysqli_query($conn, "
				delete from playlistEntries
				where
					playlistId = " . $playlists[$pi]->id . "
					and songIndex >= " . $c_playlist_entries . "
				");
				
		}
		
		mysqli_query($conn, "update playlists set active = 0 where active = 2");
		
	}

	// remove double playlist entries
	if($settings->val('remove_double_playlistentries', 'no') == 'first' || $settings->val('remove_double_playlistentries', 'no') == 'last')
	{
		$which_selection = $settings->val('remove_double_playlistentries', 'no') == 'first' ? 'max' : 'min';
		
		$qry_entries = mysqli_query($conn, "
			select
				pe.playlistId,
				pe.songId,
				count(pe.id) as doubles,
				" . $which_selection . "(pe.songIndex) as songIndex
			from playlistEntries pe
			group by
				pe.playlistId,
				pe.songId
			having
				count(pe.id) > 1
			order by
				songIndex desc
			");
		
		while($entry = mysqli_fetch_array($qry_entries)){
			$subsonic->updatePlaylistRemove($entry['playlistId'], $entry['songIndex']);
		}
	}

	

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
		$qry_genres = mysqli_query($conn, "
			select distinct
				mg.description as genre
			
			from songs s
				left join genres g on g.description = s.genre and s.genre <> ''
				join mainGenres mg on mg.id = ifnull(s.maingenreid, g.maingenreid)
			
			where
				ifnull(s.export,0) <> 0
				or s.active = 0
			");
			
		while($genre = mysqli_fetch_array($qry_genres)){
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
		$qry_songs = mysqli_query($conn, "
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
			
		while($song = mysqli_fetch_array($qry_songs)){
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
		mysqli_query($conn, "
			update songs
			set
				export = 0
			where
				export = -1
				or active = 0
			");
		
	}
	
	$task->setIsRunning(false);
	
}

?>