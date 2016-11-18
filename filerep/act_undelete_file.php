<?php
set_time_limit(0);

$active = saneInput('active', 'int', -1);

$qry_file = mysqli_query($conn, "
	select
		f.id_file,
		f.id_share,
		f.active,
		f.filename,
		f.relative_directory,
		s.server_directory
	from t_file f
	join t_share s on s.id_share = f.id_share
		and s.active = 1
	where
		f.id_file = " . $id_file . "
	");
	
$dbfile = mysqli_fetch_array($qry_file);

$file = $dbfile['server_directory'] . $dbfile['relative_directory'] . $dbfile['filename'];

if($dbfile['active'] == 0 && file_exists($file . '.deleted')){
	rename($file . '.deleted', $file);
	
	// undelete
	mysqli_query($conn, "
		update t_file 
		set active = 1
		where
			id_file = " . $dbfile['id_file'] . "
			and active = 0
		");
	
}
else if($dbfile['active'] == 0 && file_exists($file)){
	
	// undelete
	mysqli_query($conn, "
		update t_file 
		set active = 1
		where
			id_file = " . $dbfile['id_file'] . "
			and active = 0
		");
	
}
else {
	
	// remove to be reindexed
	mysqli_query($conn, "
		delete from t_file 
		where
			id_file = " . $dbfile['id_file'] . "
			and active = 0
		");
	
}

// undelete
mysqli_query($conn, "
	delete from t_file_index
	where
		id_share = " . $dbfile['id_share'] . "
		and filename = '" .  mysqli_real_escape_string($conn, $dbfile['filename']) . "'
		and relative_directory = '" .  mysqli_real_escape_string($conn, $dbfile['relative_directory']) . "'
		
	");
	

goto_action('details', false, 'id_share=' . $id_share . '&dir=' . $dir . '&all=' . $show_all );


?>