<?php
	
$qry_playlist_entries = mysqli_query($conn, "
	
	select
		pe.id,
		pe.playlistId,
		pe.songId,
		pe.songIndex,
		
		concat(
			case when s.artist = '' then '' else concat(s.artist, ' - ') end,
			s.title
		) as label,
		s.parentId,
		s.title,
		s.album,
		s.artist,
		s.track,
		s.year,
		s.genre,
		s.size,
		s.contentType,
		s.suffix,
		s.duration,
		s.bitRate,
		s.path,
		s.filename,
		s.relative_directory,
		s.isVideo,
		s.created,
		s.type,
		s.albumId
		
	from playlistEntries pe
	join songs s on s.id = pe.songId
	
	where
		pe.playlistId = " . $playlistId . "
		
	union
	
	select
		pea.id,
		pea.playlistId,
		pea.songId,
		-1 * pea.id as songIndex,
		
		concat(
			case when s.artist = '' then '' else concat(s.artist, ' - ') end,
			s.title
		) as label,
		s.parentId,
		s.title,
		s.album,
		s.artist,
		s.track,
		s.year,
		s.genre,
		s.size,
		s.contentType,
		s.suffix,
		s.duration,
		s.bitRate,
		s.path,
		s.filename,
		s.relative_directory,
		s.isVideo,
		s.created,
		s.type,
		s.albumId
		
	from playlistEntriesToAdd pea
	join songs s on s.id = pea.songId
	
	where
		pea.playlistId = " . $playlistId . "
		
		
	order by
		label
		
	");
	
?>