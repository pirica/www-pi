<?php
	
$qry_songs_recent = mysql_query("
	
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
	where
		s.isVideo = 0
		
	order by
		s.id desc
		
	limit
		" . (($page - 1) * $perpage + 1) . ", " . $perpage . "
		
	", $conn);
	
?>