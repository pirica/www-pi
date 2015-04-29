<?php
	
$qry_grab_files = mysql_query("
	
	select
		gf.id_grab_file,
		gf.active,
		
		gf.full_url,
		gf.full_path,
		gf.status,
		
		gf.date_inserted,
		gf.date_modified
		
	from t_grab_file gf
	where
		gf.id_grab = " . $id_grab . "
		and ifnull(gf.status,'') = " . ($status == '' || $status == '*' ? "ifnull(gf.status,'')" : "'" . mysql_real_escape_string($status) . "'") . "
		and (
			gf.full_url like '%" . mysql_real_escape_string($search) . "%'
			or
			gf.full_path like '%" . mysql_real_escape_string($search) . "%'
		)
		
	order by
		gf." . $sort . " " . $sortorder . "
	
	limit " . $perpage . " offset " . $offset . "
	
	", $conn);
	
?>