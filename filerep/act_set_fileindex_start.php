<?php
set_time_limit(0);

$logging = '';

$date_last_replicated = saneInput('date_last_replicated'); // seconds since epoch

if($date_last_replicated != ''){
	mysql_query("
		update t_host_share
		set
			date_last_replicated = '" . $date_last_replicated . "'
		where
			id_share = " . $id_share . " 
			and id_host = " . $id_host . " 
			and active = 1
		", $conn);
}

// mark all files as not found first
mysql_query("
	update t_file_index
	set
		notfound = 1
	
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	", $conn);
	

/*
// clear all my actions
mysql_query("
	update t_file_action
	set
		date_executed = now()
	
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	", $conn);
*/

// clear all my actions
mysql_query("
	delete from t_file_action
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	", $conn);
	
$returnvalue = array('data' => [], 'logging' => $logging);


?>