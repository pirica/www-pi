<?php

set_time_limit(0);
ini_set('max_input_time', 99999);

include 'connection.php';
include 'act_settings.php';
include 'functions.php';


// check if script is already running - no, continue
if($setting_fileindex_running == '0' && $setting_directoryindex_running == '0' && $setting_shareindex_running == '0'){
	// mark as running
	mysql_query("update t_setting set value = '1' where code = 'shareindex_running'", $conn);
	
	
	$qry_shares = mysql_query("
		select
			s.id_share,
			s.name,
			s.server_directory
		from t_share s
		where
			s.active = 1
			and s.external = 0
		
		", $conn);
		

	$id_share = -1;

	while ($share = mysql_fetch_array($qry_shares)) {
		$id_share = $share{'id_share'};
		$dir = $share{'server_directory'};
		
		
		// update share stats
		mysql_query("
			update t_share
			set
				total_files = (select count(id_file) from t_file where id_share = " . $id_share . " and active = 1),
				total_filesize = (select sum(ifnull(size,0)) from t_file where id_share = " . $id_share . " and active = 1),
				date_last_modified = (select max(date_last_modified) from t_file where id_share = " . $id_share . " and active = 1),
				
				total_files_inactive = (select count(id_file) from t_file where id_share = " . $id_share . " and active = 0),
				total_filesize_inactive = (select sum(ifnull(size,0)) from t_file where id_share = " . $id_share . " and active = 0),
					
				hosts_linked = (select count(id_host_share) from t_host_share where id_share = " . $id_share . " and id_host <> " . $setting_server_id_host . " and active = 1),
				hosts_linked_inactive = (select count(id_host_share) from t_host_share where id_share = " . $id_share . " and id_host <> " . $setting_server_id_host . " and active = 0)
				
			where
				id_share = " . $id_share . "
			", $conn);
		
		$diskspace_total = disk_total_space($share['server_directory']);
		$diskspace_free = disk_free_space($share['server_directory']);
		
		// could not check disk space (false => 0)
		if($diskspace_total === false){
			$diskspace_total = 0;
		}
		if($diskspace_free === false){
			$diskspace_free = 0;
		}
		
		// set date last replicated on share
		mysql_query("
			update t_host_share
			set
				diskspace_total = " . $diskspace_total . ",
				diskspace_free = " . $diskspace_free . "
			where
				active = 1
				and id_share = " . $id_share . "
				and id_host = " . $setting_server_id_host . " 
			", $conn);
		
	}

	// script is done, unmark as running
	mysql_query("update t_setting set value = '0' where code = 'shareindex_running'", $conn);
	
}

?>