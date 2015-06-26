<?php
$logging = '';

$sharename = mysql_real_escape_string(saneInput('sharename'));
$local_directory = mysql_real_escape_string(saneInput('local_directory'));

$check_type = mysql_real_escape_string(saneInput('check_type'));
$check_on_start = saneInput('check_on_start', 'int', '0');
$check_period = saneInput('check_period', 'int', '0');
$check_on_days = mysql_real_escape_string(saneInput('check_on_days'));
$check_on_hours = mysql_real_escape_string(saneInput('check_on_hours'));
$check_on_minutes = mysql_real_escape_string(saneInput('check_on_minutes'));

$exclude_extensions = mysql_real_escape_string(saneInput('exclude_extensions'));
$exclude_directory = mysql_real_escape_string(saneInput('exclude_directory'));
$exclude_filename = mysql_real_escape_string(saneInput('exclude_filename'));

//$compare_date_modified = saneInput('compare_date_modified', 'int', '0');
//$compare_checksum = saneInput('compare_checksum', 'int', '0');
//$cached_index = saneInput('cached_index', 'int', '0');
$max_download_speed = saneInput('max_download_speed', 'int', '0');
$remove_empty_dirs = saneInput('remove_empty_dirs', 'int', '0');
$priority = mysql_real_escape_string(saneInput('priority'));



if($sharename == '' && $id_share <= 0){
	$returnvalue = array('type' => 'error', 'message' => 'sharename is required');
}
else if($local_directory == '' && $id_host > 0){
	$returnvalue = array('type' => 'error', 'message' => 'local_directory is required');
}
else {
	
	if($id_share <= 0){
		$qry = mysql_query("
			insert into t_share (sharename)
			values ('" . $sharename . "' )
			", $conn);
		$id_share = mysql_insert_id($conn);
		$logging .= 'share created, ';
	}
	
	$qry = mysql_query("
		update t_host_share 
		set
			local_directory = '" . $local_directory . "',
			
			check_type = '" . $check_type . "',
			check_period = " . $check_period . ",
			check_on_start = " . $check_on_start . ",
			check_on_days = '" . $check_on_days . "',
			check_on_hours = '" . $check_on_hours . "',
			check_on_minutes = '" . $check_on_minutes . "',
			
			exclude_extensions = '" . $exclude_extensions . "',
			exclude_directory = '" . $exclude_directory . "',
			exclude_filename = '" . $exclude_filename . "',
			max_download_speed = " . $max_download_speed . ",
			remove_empty_dirs = " . $remove_empty_dirs . ",
			priority = '" . $priority . "'
			
		where
			active = 1
			and id_host = " . $id_host . " 
			and id_share = " . $id_share . " 
		", $conn);
	
	if(mysql_affected_rows($conn) == 0){
		
		mysql_query("
			insert into t_host_share
			(
				id_host,
				id_share,
				date_linked_since,
				
				local_directory,
				
				check_type,
				check_period,
				check_on_start,
				check_on_days,
				check_on_hours,
				check_on_minutes,
				
				exclude_extensions,
				exclude_directory,
				exclude_filename,
				max_download_speed,
				remove_empty_dirs,
				priority
			)
			select
				h.id_host,
				s.id_share,
				CURRENT_TIMESTAMP,
				
				'" . $local_directory . "',
				
				'" . $check_type . "',
				" . $check_period . ",
				" . $check_on_start . ",
				'" . $check_on_days . "',
				'" . $check_on_hours . "',
				'" . $check_on_minutes . "',
				
				'" . $exclude_extensions . "',
				'" . $exclude_directory . "',
				'" . $exclude_filename . "',
				" . $max_download_speed . ",
				" . $remove_empty_dirs . ",
				'" . $priority . "'
			
			from t_host h
			join t_share s on s.id_share = " . $id_share . "
			left join t_host_share hs on hs.id_host = h.id_host and hs.id_share = s.id_share and hs.active = 1
			
			where h.id_host = " . $id_host . "
				and hs.id_host_share is null
			
			", $conn);
			
		$logging .= 'host linked, ';
		
		
		$script_link_hostshare = $settings->val('script_link_hostshare','');
		if($script_link_hostshare != ''){
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
			$script_link_hostshare = str_replace('%id_share%', $share['id_share'], $script_link_hostshare);
			$script_link_hostshare = str_replace('%sharename%', $share['name'], $script_link_hostshare);
			$script_link_hostshare = str_replace('%server_directory%', $share['server_directory'], $script_link_hostshare);
			
			$script_link_hostshare = str_replace('%id_host%', $host['id_host'], $script_link_hostshare);
			$script_link_hostshare = str_replace('%name%', $host['name'], $script_link_hostshare);
			$script_link_hostshare = str_replace('%username%', $host['username'], $script_link_hostshare);
			$script_link_hostshare = str_replace('%os%', $host['os'], $script_link_hostshare);
			
			shell_exec($script_link_hostshare);
		}

	}
		
	$returnvalue = array('type' => 'info', 'message' => 'share updated', 'logging' => $logging);

}


?>