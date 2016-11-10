<?php

if($search != ''){
	$qry_songs_search_total = mysqli_query($conn, "
		
		select
			count(s.id) as songcount
			
		from songs s
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
		
		");

	$songs_total = mysqli_fetch_array($qry_songs_search_total);
}
else {
	$songs_total = array('songcount' => 0);
}
?>