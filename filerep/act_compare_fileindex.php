<?php
set_time_limit(0);
$debug = 0;

if(!isset($id_host)){
	include 'connection.php';
	require '../_core/functions.php';
	
	$id_host = saneInput('id_host', 'int', -1);
	$id_share = saneInput('id_share', 'int', -1);
	$logging = '';
	$debug = 1;
	
	$query_success = true;
}

// either the file is date_last_modified on server or local: down- or upload accordingly
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'upload' as action,
		concat(f.relative_directory,f.filename) as source,
		'' as target 
	from t_file f
	join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename and fi.id_share = f.id_share and fi.id_host = " . $id_host . "
		and fi.date_last_modified <> f.date_last_modified
		and abs( TIME_TO_SEC(TIMEDIFF(fi.date_last_modified, f.date_last_modified)) ) not in (0, 3600, 7200)
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.id_share = f.id_share and fa.id_host = " . $id_host . "
	where fa.id_file_action is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.notfound,0) = 0
	and ifnull(fi.excluded,0) = 0
	and fi.date_last_modified > f.date_last_modified
	");

$logging = $logging . ' up:' . mysqli_affected_rows($conn);


// either the file is date_last_modified on server or local: down- or upload accordingly
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'download' as action,
		concat(f.relative_directory,f.filename) as source,
		'' as target 
	from t_file f
	join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename and fi.id_share = f.id_share and fi.id_host = " . $id_host . "
		and fi.date_last_modified <> f.date_last_modified
		and abs( TIME_TO_SEC(TIMEDIFF(fi.date_last_modified, f.date_last_modified)) ) not in (0, 3600, 7200)
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.id_share = f.id_share and fa.id_host = " . $id_host . "
	where fa.id_file_action is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.excluded,0) = 0
	and fi.date_last_modified < f.date_last_modified
	");
	
$logging = $logging . ' updown:' . mysqli_affected_rows($conn);


// download conflict file's original version to compare locally
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'download_conflict' as action,
		concat(f.relative_directory,f.filename) as source,
		'' as target 
	from t_file f
	join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename and fi.id_share = f.id_share and fi.id_host = " . $id_host . "
		and fi.date_last_modified <> f.date_last_modified
		and abs( TIME_TO_SEC(TIMEDIFF(fi.date_last_modified, f.date_last_modified)) ) not in (0, 3600, 7200)
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.id_share = f.id_share and fa.id_host = " . $id_host . "
	where fa.id_file_action is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and fi.conflict = 1
	#and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' conflictdown:' . mysqli_affected_rows($conn);



/*
// file moved on server : move on client
mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'move' as action,
		fi.relative_directory as source,
		concat(f.relative_directory,f.filename) as target 
	from t_file_index fi
	join t_file f on fi.relative_directory <> f.relative_directory and f.filename = fi.filename
		and fi.size = f.size
		and fi.checksum = f.checksum
		and fi.date_last_modified = f.date_last_modified
	left join t_file_action fa on (fa.source = concat(fi.relative_directory,fi.filename) or fa.target = concat(fi.relative_directory,fi.filename)) and fa.date_executed is null
	where fa.id_file_action is null
	and isnull(fi.notfound,0) = 1
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' clientmove:' . mysqli_affected_rows($conn);
*/

/*
// file moved on client : move on server
mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'move' as action,
		f.relative_directory as source,
		fi.relative_directory as target 
	from t_file f
	join t_file_index fi on fi.relative_directory <> f.relative_directory  and f.filename = fi.filename ??
		and fi.size = f.size
		and fi.checksum = f.checksum
		and fi.date_last_modified = f.date_last_modified
	left join t_file_action fa on (fa.source = f.relative_directory or fa.target = f.relative_directory) and fa.date_executed is null
	where fa.id_file_action is null
	and isnull(fi.notfound,0) = 0
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' servermove:' . mysqli_affected_rows($conn);
*/

/*
// file deleted client
mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		null id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'delete' as action,
		concat(fi.relative_directory,fi.filename) as source,
		'' as target 
	from t_file_index fi
	left join t_file_action fa on fa.source = concat(fi.relative_directory,fi.filename) and fi.id_share = fa.id_share and fi.id_host = fa.id_host
	where 
		fa.id_file_action is null
		and fi.id_share = " . $id_share . "
		and fi.id_host = " . $id_host . "
		and ifnull(fi.conflict,0) = 0
		and ifnull(fi.notfound,0) = 1
		and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' delc:' . mysqli_affected_rows($conn);
*/

// file deleted server
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'delete' as action,
		concat(f.relative_directory,f.filename) as source,
		'' as target 
	from t_file_index fi
	join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.id_share = fi.id_share and f.active = 0
	left join t_file_action fa on fa.source = concat(fi.relative_directory,fi.filename) and fi.id_share = fa.id_share and fi.id_host = fa.id_host
	where 
		fa.id_file_action is null
		and fi.id_share = " . $id_share . "
		and fi.id_host = " . $id_host . "
		and ifnull(fi.conflict,0) = 0
		and ifnull(fi.notfound,0) = 0
		and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' dels:' . mysqli_affected_rows($conn);


/*
// conflicts: mark as conflict
mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'conflict' as action,
		concat(fi.relative_directory,fi.filename) as source,
		'' as target 
	from t_file_index fi
	join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.active = 1 and f.id_share = " . $id_share . "
	left join t_file_action fa on (fa.source = concat(fi.relative_directory,fi.filename) or fa.target = concat(fi.relative_directory,fi.filename)) and fa.date_executed is null
	where fa.id_file_action is null
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and fi.conflict = 1
	and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' conflict:' . mysqli_affected_rows($conn);
*/

// new file on server: download
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		f.id_file,
		f.id_share,
		" . $id_host . " as id_host,
		now(),
		'download_new' as action,
		concat(f.relative_directory,f.filename) as source,
		'' as target 
	from t_file f
	left join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename and fi.id_share = f.id_share and fi.id_host = " . $id_host . "
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.id_share = f.id_share and fa.id_host = " . $id_host . "
	where fa.id_file_action is null
		and fi.relative_directory is null
		and f.active = 1
		and f.id_share = " . $id_share . "
	");

$logging = $logging . ' downloadnew:' . mysqli_affected_rows($conn);


// new file on client: upload
$query_success = $query_success && mysqli_query($conn, "
	insert into t_file_action (id_file, id_share, id_host, date_action, action,source,target)
	select
		null id_file,
		fi.id_share,
		fi.id_host,
		now(),
		'upload_new' as action,
		concat(fi.relative_directory,fi.filename) as source,
		'' as target 
	from t_file_index fi
	left join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.active = 1 and f.id_share = fi.id_share
	left join t_file_action fa on fa.source = concat(fi.relative_directory,fi.filename) and fa.id_share = fi.id_share and fa.id_host = fi.id_host
	where fa.id_file_action is null
		and f.relative_directory is null
		and fi.id_share = " . $id_share . "
		and fi.id_host = " . $id_host . "
		and ifnull(fi.notfound,0) = 0
		and ifnull(fi.conflict,0) = 0
		and ifnull(fi.excluded,0) = 0
	");

$logging = $logging . ' uploadnew:' . mysqli_affected_rows($conn);


	
	
if($debug == 1){
	echo $logging;
}
?>