<?php

$crondate = time();

set_time_limit(3600);
require dirname(__FILE__).'/../../_core/appinit.php';
require dirname(__FILE__).'/../connection.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);

	require dirname(__FILE__).'/../act_init_subsonic.php';


	
	
	$playlists = $subsonic->getPlaylists();
	$c_playlists = count($playlists);

	if($c_playlists > 0){
		
		mysqli_query($conn, "truncate table sync_playlists");
		
		for($pi=0; $pi<$c_playlists; $pi++){
			mysqli_query($conn, "
				insert into sync_playlists
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
		
		mysqli_query($conn, "update playlists set active = 2");
		
		mysqli_query($conn, "
			insert into playlists
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
			select
				sp.id,
				sp.name,
				sp.comment,
				sp.owner,
				sp.public,
				sp.songcount,
				sp.duration,
				sp.created,
				sp.active
			from sync_playlists sp
			left join playlists p on p.id = sp.id
			where
				p.id is null
		");
		
		mysqli_query($conn, "
			update playlists p
			join sync_playlists sp on sp.id = p.id
			set
				p.name = sp.name,
				p.comment = sp.comment,
				p.owner = sp.owner,
				p.public = sp.public,
				p.songcount = sp.songcount,
				p.duration = sp.duration,
				p.created = sp.created,
				p.active = 1
			
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
		
		mysqli_query($conn, "update playlists set active = 0 where active = 2");
		
	}

	
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
	
		
	//if(date("H", $crondate) == $settings->val('subsonic_fullsync_hour', 3) && date("i", $crondate) < 5)
	{
		
		mysqli_query($conn, "
			update playlistEntriesToAdd pea
			join songs s on s.filename = pea.songFilename and s.active > 0
			set pea.songId = s.id
			");

		
		$qry_entries = mysqli_query($conn, "
			select
				pea.playlistId,
				pea.songId
			from playlistEntriesToAdd pea
				left join playlistEntries pe on pe.playlistId = pea.playlistId and pe.songId = ifnull(pea.songId, s.id)
			where 
				pea.songId is not null
				and pe.id is null
			group by
				pea.playlistId,
				pea.songId
			");
		
		while($entry = mysqli_fetch_array($qry_entries)){
			$subsonic->updatePlaylistAdd($entry['playlistId'], $entry['songId']);
		}
		
		mysqli_query($conn, "delete from playlistEntriesToAdd where playlistId = " . $entry['playlistId'] . " and songId = ". $entry['songId']);
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
	
	
	
	$task->setIsRunning(false);
	
}

?>