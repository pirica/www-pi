<?php
	
$qry_playlist_entries = mysql_query("
	
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
		
	order by
		concat(
			case when s.artist = '' then '' else concat(s.artist, ' - ') end,
			s.title
		)
		
	", $conn);
	
?>