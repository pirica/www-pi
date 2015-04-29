<?php
$logging = '';

$sharename = mysql_real_escape_string(saneInput('sharename'));
$local_directory = mysql_real_escape_string(saneInput('local_directory'));
$remove_empty_dirs = saneInput('remove_empty_dirs', 'int', '0');

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
$max_download_speed = saneInput('max_download_speed', 'int', '0');
//$cached_index = saneInput('cached_index', 'int', '0');
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
	}
		
	$returnvalue = array('type' => 'info', 'message' => 'share updated', 'logging' => $logging);

}


?>