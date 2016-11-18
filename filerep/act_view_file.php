<?php
set_time_limit(0);

$max_download_speed = saneInput('max_download_speed', 'int', '512');

$qry_file = mysqli_query($conn, "
	select
		f.id_file,
		f.filename,
		f.relative_directory,
		f.size,
		f.version,
		f.date_last_modified,
		s.server_directory,
		ifnull(m.mimetype, 'text/html') as mimetype
	from t_file f
	join t_share s on s.id_share = f.id_share
		and s.active = 1
	left join t_mimetype m on m.extension = SUBSTRING_INDEX(f.filename, '.', -1)
	where
		f.id_file = " . $id_file . "
		and f.active = 1
	");
	
$dbfile = mysqli_fetch_array($qry_file);

$file = $dbfile['server_directory'] . $dbfile['relative_directory'] . $dbfile['filename'];
$tmpfile = $file;

header('Cache-control: private');
header('Content-Type: ' . $dbfile['mimetype']);
//header('Content-Transfer-Encoding: Binary');
header('Content-disposition: inline; filename="' . $dbfile['filename'] . '"'); 
header('Content-Length: ' . $dbfile['size']);

readfile_advanced($tmpfile, 1, $max_download_speed);

flush();

?>