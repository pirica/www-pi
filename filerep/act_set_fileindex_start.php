<?php
set_time_limit(0);

$logging = '';
$query_success = true;

$date_last_replicated = saneInput('date_last_replicated'); // seconds since epoch

if($date_last_replicated != ''){
	mysqli_query($conn, "
		update t_host_share
		set
			date_last_replicated = '" . $date_last_replicated . "'
		where
			id_share = " . $id_share . " 
			and id_host = " . $id_host . " 
			and active = 1
		");
}

// remove all file move actions (should be done by now)
mysqli_query($conn, "
	delete from t_file_move
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
		
	");
	
// mark all files as not found first
$query_success = $query_success && mysqli_query($conn, "
	update t_file_index
	set
		notfound = 1
	
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	");
	

// clear all my actions
$query_success = $query_success && mysqli_query($conn, "
	delete from t_file_action
	where
		(id_share = " . $id_share . " 
		and id_host = " . $id_host . " )
		or date_action < now() - interval 5 day
	");
	
	
// clear all my actions
$query_success = $query_success && mysqli_query($conn, "
	delete from t_file_index_temp
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
		
	");
	

if($query_success){
	$returnvalue = array('data' => [], 'logging' => $logging);
}
else {
	$returnvalue = array('type' => 'error', 'logging' => $logging);
}


?>