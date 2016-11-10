<?php
	
$qry_grab_files = mysqli_query($conn, "
	
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
		and ifnull(gf.status,'') = " . ($status == '' || $status == '*' ? "ifnull(gf.status,'')" : "'" . mysqli_real_escape_string($conn, $status) . "'") . "
		and (
			gf.full_url like '%" . mysqli_real_escape_string($conn, $search) . "%'
			or
			gf.full_path like '%" . mysqli_real_escape_string($conn, $search) . "%'
		)
		
	order by
		gf." . $sort . " " . $sortorder . "
	
	limit " . $perpage . " offset " . $offset . "
	
	");
	
?>