<?php
$logging = '';

$sharename = mysqli_real_escape_string($conn, saneInput('sharename'));
$local_directory = mysqli_real_escape_string($conn, saneInput('local_directory'));

$check_type = mysqli_real_escape_string($conn, saneInput('check_type'));
$check_on_start = saneInput('check_on_start', 'int', '0');
$check_period = saneInput('check_period', 'int', '0');
$check_on_days = mysqli_real_escape_string($conn, saneInput('check_on_days'));
$check_on_hours = mysqli_real_escape_string($conn, saneInput('check_on_hours'));
$check_on_minutes = mysqli_real_escape_string($conn, saneInput('check_on_minutes'));

$exclude_extensions = mysqli_real_escape_string($conn, saneInput('exclude_extensions'));
$exclude_directory = mysqli_real_escape_string($conn, saneInput('exclude_directory'));
$exclude_filename = mysqli_real_escape_string($conn, saneInput('exclude_filename'));

//$compare_date_modified = saneInput('compare_date_modified', 'int', '0');
//$compare_checksum = saneInput('compare_checksum', 'int', '0');
//$cached_index = saneInput('cached_index', 'int', '0');
$max_download_speed = saneInput('max_download_speed', 'int', '0');
$remove_empty_dirs = saneInput('remove_empty_dirs', 'int', '0');
$priority = mysqli_real_escape_string($conn, saneInput('priority'));



if($sharename == '' && $id_share <= 0){
	$returnvalue = array('type' => 'error', 'message' => 'sharename is required');
}
else if($local_directory == '' && $id_host > 0){
	$returnvalue = array('type' => 'error', 'message' => 'local_directory is required');
}
else {
	
	if($id_share <= 0){
		$qry = mysqli_query($conn, "
			insert into t_share (sharename)
			values ('" . $sharename . "' )
			");
		$id_share = mysqli_insert_id($conn);
		$logging .= 'share created, ';
	}
	
	$qry = mysqli_query($conn, "
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
		");
	
	if(mysqli_affected_rows($conn) == 0){
		
		mysqli_query($conn, "
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
			
			");
			
		$logging .= 'host linked, ';
		
		
		$script_link_hostshare = $settings->val('script_link_hostshare','');
		if($script_link_hostshare != ''){
			$qry_share = mysqli_query($conn, "
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
					
				");
			$share = mysqli_fetch_array($qry_share);

			$qry_host = mysqli_query($conn, "
				select
					h.id_host,
					h.name,
					h.username,
					h.os,
					h.date_linked_since
				
				from t_host h
				where
					h.id_host = " . $id_host . " 
					
				");
			$host = mysqli_fetch_array($qry_host);

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