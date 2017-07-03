<?php

$crondate = time();

set_time_limit(3600);
require dirname(__FILE__).'/../../_core/appinit.php';
require dirname(__FILE__).'/../connection.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);

	require dirname(__FILE__).'/../act_init_subsonic.php';


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
		
		// custom titles
		mysqli_query($conn, "
			update songs 
			set
				title_custom = replace(
					substring(replace(filename,'_', ' '), INSTR(replace(filename,'_', ' ')," - ") + 3)
				,'_', ' ')
			where 
				contentType like 'audio/%' 
				#and active = 1 
		");
		mysqli_query($conn, "
			update songs 
			set
				title_custom = TRIM(TRAILING concat('.', suffix) FROM title_custom)
			where 
				contentType like 'audio/%' 
				#and active = 1 
		");
		
		
		// custom artist descriptions
		mysqli_query($conn, "
			update songs 
			set
				artist_custom = replace(
					LEFT(filename, INSTR(replace(filename,'_', ' ')," - ")-1)
				,'_', ' ')  
			where 
				contentType like 'audio/%' 
				#and active = 1 
				and replace(filename,'_', ' ') like '% - %' 
				#and ifnull(artist_custom,'') = '' 
		");
		
		mysqli_query($conn, "
			update songs 
			set
				artist_custom = replace(
					substring(artist_custom, INSTR(artist_custom,". ") + 2)
				,'_', ' ')  
			where 
				contentType like 'audio/%' 
				#and active = 1 
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
			insert into artists (description_orig, description, songs)
			select artist_custom, artist_custom, count(s.id) from songs s
			left join artists a on a.description_orig = s.artist_custom
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
					description = replace(description_orig, '" . $a_articles[$i] . " ', '')
				where 
					prefix = ''
					and left(description_orig, " . (strlen($a_articles[$i]) + 1) . ") = '" . $a_articles[$i] . " '
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
					where songs.artist_custom = artists.description_orig
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
				select distinct " . $settings->val('intake_playlist', -1) . ", s.id from songs s
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


	
	$task->setIsRunning(false);
	
}

?>