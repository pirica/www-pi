<?php

$qry_shares = mysqli_query($conn, "
	select
		s.id_share,
		s.name,
		s.info,
		s.server_directory,
		s.total_files as nbr_files,
		s.total_filesize as total_file_size,
		s.date_last_modified as max_date_last_modified
	
	from t_share s
		
	where
		s.active = 1
		
	order by
		s.name
		
	");
	
?>