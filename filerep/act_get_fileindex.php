<?php
set_time_limit(0);



$sharename = saneInput('sharename');
$date_last_replicated = saneInput('date_last_replicated', 'unixtime', 0); // seconds since epoch
//$cached_index = saneInput('cached_index', 'int', 1);

$qry = mysqli_query($conn, "
	select * from t_share s
	left join t_host_share hs on hs.id_share = s.id_share
		and hs.active = 1
		and hs.id_host = " . $id_host . " 
	where
		s.id_share = " . $id_share . " 
	");

while ($row = mysqli_fetch_array($qry)) {
	$sharename = $row{'server_directory'};
}

$data = [];

if(mysqli_num_rows($qry) == 0){
	$returnvalue = array('type' => 'error', 'message' => 'share not linked to host');
}
else if($sharename == ''){
	$returnvalue = array('type' => 'error', 'message' => 'server directory not set');
}
else {
	$fulldir = $sharename;
	
	$logging = 'dir='.$fulldir;
	
	/*if($cached_index == 1){
		$qry = mysqli_query($conn, "
			select
				f.filename as name,
				f.relative_directory as nativepath,
				f.size,
				f.checksum,
				f.date_last_modified as modified
			from t_file f
			where
				f.id_share = " . $id_share . " 
				and f.active = 1
			");
		$data = mysql2json($qry);
	}
	else {*/
		list_dir_shell($data, $fulldir, $date_last_replicated);
	//}
	$returnvalue = array('data' => $data, 'logging' => $logging);
}

?>