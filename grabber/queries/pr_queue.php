<?php
	
$qry_queue = mysqli_query($conn, "

	select
		q.id_queue,
		q.url,
		q.date_added,
		q.filename,
		q.directory,
		q.status
		
	from t_queue q
	where
		q.status in ('N', 'Y', 'F')
		and q.filename <> ''
		
	");
	
?>