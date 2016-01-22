<?php

if($search != ''){
	$qry_songs_search_total = mysql_query("
		
		select
			count(s.id) as songcount
			
		from songs s
		where
			s.isVideo = 0
			and (
				ifnull(s.title,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.album,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.artist,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.path,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.filename,'') like '%" . mysql_real_escape_string($search) . "%'
				or ifnull(s.relative_directory,'') like '%" . mysql_real_escape_string($search) . "%'
			)
		
		", $conn);

	$songs_total = mysql_fetch_array($qry_songs_search_total);
}
else {
	$songs_total = 0;
}
?>