<?php
include 'act_settings.php';

$qry = mysql_query("
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
		s.hosts_linked_inactive,
	
		hs.id_host_share,
		hs.id_host,
		hs.local_directory,
		hs.date_linked_since,
		hs.date_last_replicated,
		
		hs.check_type,
		hs.check_period,
		hs.check_on_start,
		hs.check_on_days,
		hs.check_on_hours,
		hs.check_on_minutes,
		
		hs.exclude_extensions,
		hs.exclude_directory,
		hs.exclude_filename,
		
		hs.max_download_speed,
		hs.remove_empty_dirs,
		hs.priority,
		
		hss.diskspace_total as server_diskspace_total,
		hss.diskspace_free as server_diskspace_free
	
	from t_share s
	left join t_host_share hs on hs.id_share = s.id_share
		and hs.active = 1
		and hs.id_host = " . $id_host . " 
	join t_host_share hss on hss.id_share = s.id_share
		and hss.active = 1
		and hss.id_host = " . $setting_server_id_host . " 
	where
		s.active = 1
	", $conn);
	
$returnvalue = array('data' => mysql2json($qry));

?>