<?php
set_time_limit(0);

$max_download_speed = saneInput('max_download_speed', 'int', '256');

$qry_file = mysql_query("
	select
		f.id_file,
		f.filename,
		f.relative_directory,
		f.size,
		f.version,
		f.date_last_modified,
		s.server_directory
	from t_file f
	join t_share s on s.id_share = f.id_share
		and s.active = 1
	where
		f.id_file = " . $id_file . "
		and f.active = 1
	", $conn);
	
$dbfile = mysql_fetch_array($qry_file);

$file = $dbfile['server_directory'] . $dbfile['relative_directory'] . $dbfile['filename'];
//$tmpfile = '/var/tmp/filerep_' . microtime(true) . '.tmp';
$tmpfile = $file;

//shell_exec('file.sh cp ' . $file . ' ' . $tmpfile);

header('Cache-control: private');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: Binary');
header('Content-disposition: attachment; filename="' . $dbfile['filename'] . '"'); 
header('Content-Length: '.$dbfile['size']);
	
//readfile($tmpfile);
readfile_advanced($tmpfile, 1, $max_download_speed);

flush();

/*mysql_query("
	insert into t_file_log
	(
		id_file,
		id_host,
		date_log,
		text_log
	)
	values
	(
		" . $id_file . ",
		" . $id_host . ",
		now(),
		'File downloaded by host'
	)
	", $conn);*/

//shell_exec('file.sh rm ' . $tmpfile);

?>