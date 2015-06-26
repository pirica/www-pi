<?php
$logging = '';

$qry = mysql_query("
	update t_host_share 
	set
		active = 0,
		date_unlinked = now()
	where
		active = 1
		and id_host = " . $id_host . " 
		and id_share = " . $id_share . " 
	", $conn);

$returnvalue = array('type' => 'info', 'message' => 'share unlinked', 'logging' => $logging);


$script_unlink_hostshare = $settings->val('script_unlink_hostshare','');
if($script_unlink_hostshare != ''){
	$qry_share = mysql_query("
		select
			s.id_share,
			s.name,
			s.info,
			s.server_directory,
			
			s.total_files,
			s.total_filesize,
			s.date_last_modified,
			s.total_files_inactive,
			s.total_filesize_inactive,
			s.hosts_linked,
			s.hosts_linked_inactive
		
		from t_share s
		where
			s.id_share = " . $id_share . " 
			
		", $conn);
	$share = mysql_fetch_array($qry_share);

	$qry_host = mysql_query("
		select
			h.id_host,
			h.name,
			h.username,
			h.os,
			h.date_linked_since
		
		from t_host h
		where
			h.id_host = " . $id_host . " 
			
		", $conn);
	$host = mysql_fetch_array($qry_host);

	// and execute any scripts on completion
	$script_unlink_hostshare = str_replace('%id_share%', $share['id_share'], $script_unlink_hostshare);
	$script_unlink_hostshare = str_replace('%sharename%', $share['name'], $script_unlink_hostshare);
	$script_unlink_hostshare = str_replace('%server_directory%', $share['server_directory'], $script_unlink_hostshare);
	
	$script_unlink_hostshare = str_replace('%id_host%', $host['id_host'], $script_unlink_hostshare);
	$script_unlink_hostshare = str_replace('%name%', $host['name'], $script_unlink_hostshare);
	$script_unlink_hostshare = str_replace('%username%', $host['username'], $script_unlink_hostshare);
	$script_unlink_hostshare = str_replace('%os%', $host['os'], $script_unlink_hostshare);
	
	shell_exec($script_unlink_hostshare);
}

?>