<?php

$qry_share_stats = mysql_query("
	select
		s.id_share,
		s.name,
		s.info,
		s.server_directory,
		s.hosts_linked,
		s.total_files as nbr_files,
		s.total_filesize as total_file_size,
		s.date_last_modified as max_date_last_modified,
		count(d.id_directory) as nbr_dirs,
		sum(case when d.date_last_checked is null then 0 else 1 end) as dirs_checked
	
	from t_share s
		left join t_directory d on d.id_share = s.id_share
			and d.active = 1
	where
		s.active = 1
		" .
		($_SESSION['full_access'] == 1 ? '' : " and s.id_user = " . $_SESSION['user_id'] )
		. "
	group by
		s.id_share,
		s.name,
		s.info,
		s.server_directory,
		s.hosts_linked,
		s.total_files,
		s.total_filesize,
		s.date_last_modified 
		
	order by
		s.name
		
	", $conn);
	
?>