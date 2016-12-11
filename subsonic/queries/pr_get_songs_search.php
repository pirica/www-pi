<?php
	
if($search != ''){
	$qry_songs_str = "
		
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
		" . 
		($playlist == 'n' || $playlist == 'ex' ? "left join playlistEntries pe on pe.songId = s.id " : "") .
		($playlist == 'ex' ? "	and pe.id = " . $playlistId : "").
		($playlist == 'in' ? "join playlistEntries pe on pe.songId = s.id " : "") .
		($playlist == 'in' ? "	and pe.id = " . $playlistId : "").
		($mainGenreId > 0 ? "join genres g on g.description = s.genre " : "").
		
		"
		where
			s.isVideo = 0
			and (
				ifnull(s.title,'') like '%" . mysqli_real_escape_string($conn, $search) . "%'
				or ifnull(s.album,'') like '%" . mysqli_real_escape_string($conn, $search) . "%'
				or ifnull(s.artist,'') like '%" . mysqli_real_escape_string($conn, $search) . "%'
				or ifnull(s.path,'') like '%" . mysqli_real_escape_string($conn, $search) . "%'
				or ifnull(s.filename,'') like '%" . mysqli_real_escape_string($conn, $search) . "%'
				or ifnull(s.relative_directory,'') like '%" . mysqli_real_escape_string($conn, $search) . "%'
			) 
			".
			($playlist == 'n' || $playlist == 'ex' ? "and pe.id is null " : "").
			($mainGenreId > 0 ? "and ifnull(s.mainGenreId, g.mainGenreId) = " . $mainGenreId : "").
			
		"
		order by
			s.id desc
			
		limit " . $perpage . " offset " . $offset . "
			
		";
		
	echo '<!--' . $qry_songs_str . '-->';
	
	$qry_songs = mysqli_query($conn, $qry_songs_str);
}
else {
	$qry_songs = mysqli_query($conn, "
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
			
		");
}
?>