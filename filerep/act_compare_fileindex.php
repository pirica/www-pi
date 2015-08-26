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
}

// either the file is date_last_modified on server or local: down- or upload accordingly
mysql_query("
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
	join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename
		and fi.date_last_modified <> f.date_last_modified
		and abs( TIME_TO_SEC(TIMEDIFF(fi.date_last_modified, f.date_last_modified)) ) <> 3600
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.date_executed is null
	where fa.id_file_action is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.notfound,0) = 0
	and ifnull(fi.excluded,0) = 0
	and fi.date_last_modified > f.date_last_modified
	", $conn);

$logging = $logging . ' up:' . mysql_affected_rows($conn);


// either the file is date_last_modified on server or local: down- or upload accordingly
mysql_query("
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
	join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename
		and fi.date_last_modified <> f.date_last_modified
		and abs( TIME_TO_SEC(TIMEDIFF(fi.date_last_modified, f.date_last_modified)) ) <> 3600
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.date_executed is null
	where fa.id_file_action is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.excluded,0) = 0
	and fi.date_last_modified < f.date_last_modified
	", $conn);
	
$logging = $logging . ' updown:' . mysql_affected_rows($conn);


// download conflict file's original version to compare locally
mysql_query("
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
	join t_file_index fi on fi.relative_directory = f.relative_directory and f.filename = fi.filename
		and fi.date_last_modified <> f.date_last_modified
		and abs( TIME_TO_SEC(TIMEDIFF(fi.date_last_modified, f.date_last_modified)) ) <> 3600
	left join t_file_action fa on fa.source = concat(f.relative_directory,f.filename) and fa.date_executed is null
	where fa.id_file_action is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and fi.conflict = 1
	#and ifnull(fi.excluded,0) = 0
	", $conn);

$logging = $logging . ' conflictdown:' . mysql_affected_rows($conn);



/*
// file moved on server : move on client
mysql_query("
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
	", $conn);

$logging = $logging . ' clientmove:' . mysql_affected_rows($conn);
*/

/*
// file moved on client : move on server
mysql_query("
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
	", $conn);

$logging = $logging . ' servermove:' . mysql_affected_rows($conn);
*/


// file deleted client
mysql_query("
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
	left join t_file_action fa on fa.source = concat(fi.relative_directory,fi.filename) and fa.date_executed is null
	where fa.id_file_action is null
	and fi.notfound = 1
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.notfound,0) = 0
	and ifnull(fi.excluded,0) = 0
	", $conn);

$logging = $logging . ' delc:' . mysql_affected_rows($conn);


// file deleted server
mysql_query("
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
	where 
	fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and fi.notfound = 0
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.excluded,0) = 0
	", $conn);

$logging = $logging . ' dels:' . mysql_affected_rows($conn);


/*
// conflicts: mark as conflict
mysql_query("
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
	", $conn);

$logging = $logging . ' conflict:' . mysql_affected_rows($conn);
*/

// new file on server: download
mysql_query("
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
	left join t_file_action fa on (fa.source = concat(f.relative_directory,f.filename) or fa.target = concat(f.relative_directory,f.filename)) and fa.date_executed is null
	where fa.id_file_action is null
	and fi.relative_directory is null
	and f.active = 1
	and f.id_share = " . $id_share . "
	", $conn);

$logging = $logging . ' downloadnew:' . mysql_affected_rows($conn);


// new file on client: upload
mysql_query("
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
	left join t_file f on f.relative_directory = fi.relative_directory and f.filename = fi.filename and f.active = 1 and f.id_share = " . $id_share . "
	left join t_file_action fa on (fa.source = concat(fi.relative_directory,fi.filename) or fa.target = concat(fi.relative_directory,fi.filename)) and fa.date_executed is null
	where fa.id_file_action is null
	and f.relative_directory is null
	and fi.id_share = " . $id_share . "
	and fi.id_host = " . $id_host . "
	and ifnull(fi.notfound,0) = 0
	and ifnull(fi.conflict,0) = 0
	and ifnull(fi.excluded,0) = 0
	", $conn);

$logging = $logging . ' uploadnew:' . mysql_affected_rows($conn);


	
	
if($debug == 1){
	echo $logging;
}
?>