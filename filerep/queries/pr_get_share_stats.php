<?php

$qry_share_stats = mysql_query("
	select
		s.id_share,
		s.name,
		s.info,
		s.server_directory,
		count(distinct hs.id_host_share) - 1 as hosts_linked,
		max(ifnull(hs.date_last_replicated,0)) as max_date_last_replicated,
		#sum(case when f.id_file is null then 0 else 1 end) as nbr_files,
		s.total_files as nbr_files,
		#sum(ifnull(f.size,0)) as total_file_size,
		s.total_filesize as total_file_size,
		#max(ifnull(f.date_last_modified,0)) as max_date_last_modified
		s.date_last_modified as max_date_last_modified
	
	from t_share s
		join t_host_share hs on hs.id_share = s.id_share
			and hs.active = 1
		#left join t_file f on f.id_share = s.id_share
	
	where
		s.active = 1
		
	group by
		s.id_share,
		s.name,
		s.info,
		s.server_directory
		
	", $conn);
	
?>