<?php
	
$qry_playlists = mysql_query("
	
	select
		p.id,
		p.name,
		p.comment,
		p.owner,
		p.public,
		p.songcount,
		p.duration,
		p.created
		
	from playlists p
	
	order by
		p.name
		
	", $conn);
	
?>