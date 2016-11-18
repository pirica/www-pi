<?php
set_time_limit(0);
$debug = 0;

if(!isset($id_host)){
	include 'functions.php';
	
	$id_host = saneInput('id_host', 'int', -1);
	$id_share = saneInput('id_share', 'int', -1);
	$logging = '';
	$debug = 1;
	
	$query_success = true;
}

// delete file on server
$query_success = $query_success && mysqli_query($conn, "
	update t_file f
	join t_file_index fi on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.id_share = fi.id_share and fi.id_host = " . $id_host . " 
		and fi.notfound = 1
	set
		f.active = 0
	where
		f.id_share = " . $id_share . " 
		and f.active = 1
	
	");

$logging = $logging . ' fdels:' . mysqli_affected_rows($conn);


	
	
if($debug == 1){
	echo $logging;
}
?>