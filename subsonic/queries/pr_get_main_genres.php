<?php
	
$qry_main_genres = mysqli_query($conn, "
	
	select
		id,
		description
		
	from mainGenres
	
	order by
		description
		
	");
	
?>