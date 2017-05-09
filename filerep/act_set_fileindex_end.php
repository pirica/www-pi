<?php
set_time_limit(0);

$logging = '';

$download_only = 0;

$modifiedcount = 0;
$insertcount = 0;
$conflictcount = 0;
$dpmod_newc_count = 0;
$dpmod_nonc_count = 0;
$dpmod_nonc_c_count = 0;

$query_success = true;


$qry = mysqli_query($conn, "
	select * from t_share s
	left join t_host_share hs on hs.id_share = s.id_share
		and hs.active = 1
		and hs.id_host = " . $id_host . " 
	where
		s.id_share = " . $id_share . " 
	");

while ($row = mysqli_fetch_array($qry)) {
	$download_only = $row{'download_only'};
}


// update date_previous_modified = date_last_modified where conflmict=0 + temp conflict = 1
$query_success = $query_success && mysqli_query($conn, "
	update t_file_index fi
	join t_file_index_temp fit
		on fit.id_share = fi.id_share
		and fit.id_host = fi.id_host
		and fit.relative_directory = fi.relative_directory
		and fit.filename = fi.filename
		and fit.conflict = 1
	set
		fi.date_previous_modified = fi.date_last_modified
	where
		fi.id_share = " . $id_share . " 
		and fi.id_host = " . $id_host . " 
		and fi.conflict = 0
	");
$dpmod_newc_count = $dpmod_newc_count + mysqli_affected_rows($conn);

// update date_previous_modified = date_last_modified where conflmict=0 + temp conflict = 0
$query_success = $query_success && mysqli_query($conn, "
	update t_file_index fi
	join t_file_index_temp fit
		on fit.id_share = fi.id_share
		and fit.id_host = fi.id_host
		and fit.relative_directory = fi.relative_directory
		and fit.filename = fi.filename
		and fit.conflict = 0
	set
		fi.date_previous_modified = fi.date_last_modified
	where
		fi.id_share = " . $id_share . " 
		and fi.id_host = " . $id_host . " 
		and fi.conflict = 0
	");
$dpmod_nonc_count = $dpmod_nonc_count + mysqli_affected_rows($conn);

// update conflict = 0,date_previous_modified = date_last_modified where conflmict=1 + temp conflict = 0
$query_success = $query_success && mysqli_query($conn, "
	update t_file_index fi
	join t_file_index_temp fit
		on fit.id_share = fi.id_share
		and fit.id_host = fi.id_host
		and fit.relative_directory = fi.relative_directory
		and fit.filename = fi.filename
		and fit.conflict = 1
	set
		fi.date_previous_modified = fi.date_last_modified
	where
		fi.id_share = " . $id_share . " 
		and fi.id_host = " . $id_host . " 
		and fi.conflict = 1
	");
$dpmod_nonc_c_count = $dpmod_nonc_c_count + mysqli_affected_rows($conn);




$query_success = $query_success && mysqli_query($conn, "
	update t_file_index fi
	join t_file_index_temp fit
		on fit.id_share = fi.id_share
		and fit.id_host = fi.id_host
		and fit.relative_directory = fi.relative_directory
		and fit.filename = fi.filename
	set
		fi.date_last_modified = fit.date_last_modified,
		fi.notfound = 0
	where
		fi.id_share = " . $id_share . " 
		and fi.id_host = " . $id_host . " 
		
	");
	
	
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_index
	(
		relative_directory,
		filename,
		date_last_modified,
		date_previous_modified,
		notfound,
		conflict,
		excluded,
	
		id_share,
		id_host
	)
	select
		fit.relative_directory,
		fit.filename,
		fit.date_last_modified,
		fit.date_last_modified,
		0,
		fit.conflict,
		fit.excluded,
	
		fit.id_share,
		fit.id_host
		
	from t_file_index_temp fit
	left join t_file_index fi
		on fit.id_share = fi.id_share
		and fit.id_host = fi.id_host
		and fit.relative_directory = fi.relative_directory
		and fit.filename = fi.filename
	where
		fit.id_share = " . $id_share . " 
		and fit.id_host = " . $id_host . " 
		and fi.id_file_index is null
		
	");
				
/*	
// set as conflicting where t_file date modified is already greater than current index
$query_success = $query_success && mysqli_query($conn, "
	update t_file_index fi
	join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.id_share = fi.id_share and f.active = 1 
		and f.date_last_modified > fi.date_previous_modified
		and f.date_last_modified < fi.date_last_modified
	set
		conflict = 1
	where
		fi.id_share = " . $id_share . " 
	
	");

$conflictcount = $conflictcount + mysqli_affected_rows($conn);
*/

$logging = $logging . ' confl:' . $conflictcount;

$logging = $logging . ' newconfl:' . $dpmod_newc_count;
$logging = $logging . ' nonconfl:' . $dpmod_nonc_count;
$logging = $logging . ' nonconflnew:' . $dpmod_nonc_c_count;

include 'act_server_fileindex.php';

include 'act_compare_fileindex.php';

$qry = mysqli_query($conn, "
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
		" . ($download_only == 1 ? "and fa.action in ('download', 'download_new', 'delete')" : "") . "
		
	");
$data = mysql2json($qry);

if($query_success){
	$returnvalue = array('data' => $data, 'logging' => $logging);
}
else {
	$returnvalue = array('type' => 'error', 'logging' => $logging);
}

?>