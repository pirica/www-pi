<?php
	
$qry_playlists = mysqli_query($conn, "
	
	select
		p.id,
		p.name,
		p.comment,
		p.owner,
		p.public,
		p.songcount,
		p.duration,
		p.created
		
	from subsonic.playlists p
	
	order by
		p.name
		
	");
	
?>