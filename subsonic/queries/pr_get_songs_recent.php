<?php
	
$qry_songs = mysql_query("
	
	select
		s.id as songId,
		
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
		
	from songs s
	left join playlistEntries pe on pe.songId = s.id
	
	where
		s.isVideo = 0
		and pe.id is null
		
	order by
		s.id desc
		
	limit " . $perpage . " offset " . $offset . "
		
	", $conn);
	
?>