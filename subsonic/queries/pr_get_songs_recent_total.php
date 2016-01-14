<?php
	
$qry_songs_recent_total = mysql_query("
	
	select
		count(s.id) as songcount
		
	from songs s
	where
		s.isVideo = 0
	
	", $conn);

$songs_total = mysql_fetch_array($qry_songs_recent_total);

?>