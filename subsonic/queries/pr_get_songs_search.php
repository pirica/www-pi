<?php
	
if($search != ''){
	
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
			and (
				ifnull(s.title,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.album,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.artist,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.path,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.filename,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.relative_directory,'') like '%" . mysql_real_escape_string($search) . "%'
			)
			
		order by
			s.id desc
			
		limit
			" . (($page - 1) * ($perpage + 1)) . ", " . $perpage . "
			
		", $conn);
}
else {
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
		where
			s.isVideo = 0
			
		limit 0, 0
			
		", $conn);
}
?>