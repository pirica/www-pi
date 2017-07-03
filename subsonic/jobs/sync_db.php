<?php

$crondate = time();

set_time_limit(3600);
require dirname(__FILE__).'/../../_core/appinit.php';
require dirname(__FILE__).'/../connection.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);

	require dirname(__FILE__).'/../act_init_subsonic.php';



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
		/*$qry_genres = mysqli_query($conn, "
			select distinct
				mg.description as genre
			
			from songs s
				left join genres g on g.description = s.genre and s.genre <> ''
				join mainGenres mg on mg.id = ifnull(s.maingenreid, g.maingenreid)
			
			where
				ifnull(s.export,0) <> 0
				or s.active = 0
			");*/
			
		// check and create genre dirs
		$qry_genres = mysqli_query($conn, "
			select distinct
				p.name as genre
			
			from songs s
				join playlistEntries pe on pe.songid = s.id
				join playlists p on p.id = pe.playlistId
			where
				ifnull(p.export,0) <> 0
				
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
		/*$qry_songs = mysqli_query($conn, "
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
			");*/
		
		$qry_songs = mysqli_query($conn, "
			select
				s.id,
				s.path,
				s.filename,
				s.relative_directory,
				s.artist_custom,
				s.title_custom,
				s.suffix,
				p.export,
				s.active,
				p.name as genre
			
			from songs s
				join playlistEntries pe on pe.songid = s.id
				join playlists p on p.id = pe.playlistId
			where
				ifnull(p.export,0) <> 0
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
			$filename = $song['filename'];
			
			if($song['artist_custom'] != '' && $song['title_custom'] != '')
			{
				$filename = $song['artist_custom'] . ' - ' . $song['title_custom'] . '.' . $song['suffix'];
			}
			
			if($song['active'] == 1 && $song['export'] == 1)
			{
				if(!file_exists($genre_dir . '/' . $filename) && file_exists($songs_dir . $song['path']))
				{
					if(file_exists($genre_dir. '/' . $filename . '.deleted'))
					{
						unlink($genre_dir . '/' . $filename . '.deleted');
					}
					else
					{
						copy($songs_dir . $song['path'], $genre_dir . '/' . $filename);
					}
				}
			}
			else 
			{
				if(file_exists($genre_dir . '/' . $filename))
				{
					unlink($genre_dir . '/' . $filename);
				}
				else if(file_exists($genre_dir. '/' . $filename . '.deleted'))
				{
					unlink($genre_dir . '/' . $filename . '.deleted');
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