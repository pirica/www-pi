<?php
set_time_limit(0);

$active = saneInput('active', 'int', -1);

$qry_file = mysqli_query($conn, "
	select
		f.id_file,
		f.active,
		f.filename,
		f.relative_directory,
		s.server_directory,
		s.readonly
	from t_file f
	join t_share s on s.id_share = f.id_share
		and s.active = 1
	where
		f.id_file = " . $id_file . "
	");
	
$dbfile = mysqli_fetch_array($qry_file);

$file = $dbfile['server_directory'] . $dbfile['relative_directory'] . $dbfile['filename'];

if($dbfile['active'] == $active && $dbfile['readonly'] != 1){
	if($active == 1){
		rename($file, $file . '.deleted');
		
		// mark as deleted
		mysqli_query($conn, "
			update t_file 
			set
				date_deleted = now(),
				active = 0
			where
				id_file = " . $dbfile['id_file'] . "
			");
	}
	else {
		if(file_exists($file . '.deleted')){
			unlink($file . '.deleted');
		}
		// delete
		mysqli_query($conn, "
			delete from t_file 
			where
				id_file = " . $dbfile['id_file'] . "
				and active = 0
			");
	}
}

goto_action('details', false, 'id_share=' . $id_share . '&dir=' . $dir . '&all=' . $show_all );


?>