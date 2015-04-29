<?php

set_time_limit(0);


$days = saneInput('days', 'int', '3');
$no_initial_history = saneInput('no_initial_history', 'int', '0'); // 0 or 1

$qry = mysql_query("
	select
		fl.id_file_log,
		fl.date_log,
		
		fl.id_file,
		f.active as file_active,
		
		fl.id_host,
		h.active as host_active,
		h.name as host_name,
		
		s.id_share,
		s.active as share_active,
		
		fl.date_log,
		fl.text_log,
		
		fl.size,
		fl.version,
		fl.date_last_modified,
		
		f.relative_directory + f.filename as relative_directory,
		s.name as share_name
	
	from t_file_log fl
		join t_host h on h.id_host = fl.id_host
		join t_file f on f.id_file = fl.id_file
		join t_share s on s.id_share = f.id_share
	where
		fl.active = 1
		and fl.date_log between  now() - interval " . $days . " day and now()
		and fl.version > " . $no_initial_history . "
	", $conn);
	
$returnvalue = array('data' => mysql2json($qry));

?>