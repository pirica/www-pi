<?php
set_time_limit(0);

$active = saneInput('active', 'int', -1);

$qry_file = mysql_query("
	select
		f.id_file,
		f.active,
		f.filename,
		f.relative_directory,
		s.server_directory
	from t_file f
	join t_share s on s.id_share = f.id_share
		and s.active = 1
	where
		f.id_file = " . $id_file . "
	", $conn);
	
$dbfile = mysql_fetch_array($qry_file);

$file = $dbfile['server_directory'] . $dbfile['relative_directory'] . $dbfile['filename'];

if($dbfile['active'] == 0 && file_exists($file . '.deleted')){
	rename($file . '.deleted', $file);
	
	// undelete
	mysql_query("
		update t_file 
		set active = 1
		where
			id_file = " . $dbfile['id_file'] . "
			and active = 0
		", $conn);
	
}
else if($dbfile['active'] == 0 && file_exists($file)){
	
	// undelete
	mysql_query("
		update t_file 
		set active = 1
		where
			id_file = " . $dbfile['id_file'] . "
			and active = 0
		", $conn);
	
}
else {
	
	// remove to be reindexed
	mysql_query("
		delete from t_file 
		where
			id_file = " . $dbfile['id_file'] . "
			and active = 0
		", $conn);
	
}

// undelete
mysql_query("
	delete from t_file_index
	where
		id_share = " . $dbfile['id_share'] . "
		and filename = '" .  mysql_real_escape_string($dbfile['filename']) . "'
		and relative_directory = '" .  mysql_real_escape_string($dbfile['relative_directory']) . "'
		
	", $conn);
	

goto_action('details', false, 'id_share=' . $id_share . '&dir=' . $dir . '&all=' . $show_all );


?>