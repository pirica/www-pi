<?php
set_time_limit(0);

$logging = '';

$modifiedcount = 0;
$insertcount = 0;
$conflictcount = 0;
$dpmod_newc_count = 0;
$dpmod_nonc_count = 0;
$dpmod_nonc_c_count = 0;


// set as conflicting where t_file date modified is already greater than current index
mysql_query("
	update t_file_index fi
	join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.id_share = fi.id_share and f.active = 1 
		and f.date_last_modified > fi.date_previous_modified
		and f.date_last_modified < fi.date_last_modified
	set
		conflict = 1
	where
		fi.id_share = " . $id_share . " 
	
	", $conn);

$conflictcount = $conflictcount + mysql_affected_rows($conn);


$logging = $logging . ' confl:' . $conflictcount;

$logging = $logging . ' newconfl:' . $dpmod_newc_count;
$logging = $logging . ' nonconfl:' . $dpmod_nonc_count;
$logging = $logging . ' nonconflnew:' . $dpmod_nonc_c_count;

include 'act_server_fileindex.php';

include 'act_compare_fileindex.php';

$qry = mysql_query("
	select
		fa.id_file,
		fa.id_file_action,
		fa.date_action,
		fa.action,
		fa.source,
		fa.target,
		f.date_last_modified as modified

	from t_file_action fa
		left join t_file f on f.id_file = fa.id_file
	
	where
		fa.id_share = " . $id_share . " 
		and fa.id_host = " . $id_host . " 
		and fa.active = 1
		and fa.date_executed is null
	", $conn);
$data = mysql2json($qry);

$returnvalue = array('data' => $data, 'logging' => $logging);


?>