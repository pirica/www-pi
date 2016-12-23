<?php
	
$qry_songs_recent_total = mysqli_query($conn, "
	
	select
		count(s.id) as songcount
		
	from songs s
	where
		s.isVideo = 0
		and s.active > 0
	
	");

$songs_total = mysqli_fetch_array($qry_songs_recent_total);

?>