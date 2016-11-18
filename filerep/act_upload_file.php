<?php
set_time_limit(0);

// Read the PHP input stream and save the contents to $rawPost.
$rawPost = file_get_contents('php://input');

$path = urldecode(saneInput('path'));
$chunk = saneInput('chunk', 'int');
$last = saneInput('last', 'bit', false);
$fileId = saneInput('id');
$modified = saneInput('modified', 'int', 0);

$errors = array();

if(empty($rawPost)) $errors[] = 'You did not send any POST data.';
if(empty($path)) $errors[] = 'You did not specify a file name in the url.';

/*if(substr($path, -1, 1) != '/'){
	$path = $path . '/';
}*/

// rename to temporary file
$temppath = $path . '.filerep' . $fileId;

// create directory if not exists
$parts = explode('/', $temppath, -1);
$dir = '';
foreach($parts as $part){
	if(!is_dir($dir .= "/$part")) mkdir($dir);
}

if(empty($errors)) {
    if(file_put_contents($temppath, $rawPost, FILE_APPEND) !== false){
		if($last){
			
			// rename 
			shell_exec('mv "' . $temppath . '" "' . $path . '"');
			shell_exec('chmod +w "' . $path . '"');
			
			// set date modified
			if($modified > 0){
				touch($path, $modified);
			}
			
			// insert into database
			
			$qry_shares = mysqli_query($conn, "
				select
					s.id_share,
					s.name,
					s.info,
					s.server_directory
				from t_share s
				where
					s.id_share = " . $id_share . "
				");
			$share = mysqli_fetch_array($qry_shares);
			$dir = $share['server_directory'];
			
			$reldir = $path;
			
			// remove server dir - only first instance
			$reldir = implode('', explode($dir, $reldir, 2));
			
			$filenamearr = explode('/', $reldir);
			$filename = array_pop($filenamearr);
			$reldir = implode('/', $filenamearr);
			
			if($reldir == ''){
				$reldir = '/';
			}
			if(substr($reldir, -1, 1) != '/'){
				$reldir = $reldir . '/';
			}
			
			
			mysqli_query($conn, "
				update t_directory
				set
					date_last_checked = null
				
				where
					id_share = " . $id_share . "
					and relative_directory = '" . mysqli_real_escape_string($conn, $reldir) . "'
				");
				
			/*
			$qry_files = mysqli_query($conn, "
				select
					f.id_file,
					f.filename,
					f.relative_directory,
					f.size,
					f.version,
					f.date_last_modified,
					f.date_deleted,
					f.active
				from t_file f
				where
					f.relative_directory = '" . mysqli_real_escape_string($conn, $reldir) . "'
					and f.filename = '" . mysqli_real_escape_string($conn, $filename) . "'
					and f.active = 1
				");
			
			$file_found = 0;
			
			$filesize = filesize($path);
			
			//TODO: remove filename from relative_directory
			
			while ($dbfile = mysqli_fetch_array($qry_files)) {
				if($reldir == $dbfile['relative_directory'] && $filename == $dbfile['filename']){
					$file_found = 1;
					
					// make db timestamp into unix time
					$date_last_modified = $dbfile['date_last_modified'];
					$date_last_modified = str_replace('-', '/', $date_last_modified);
					$date_last_modified = explode('.', $date_last_modified)[0];
					$date_last_modified = strtotime($date_last_modified . ' ' . timezone_offset_string());
					
					if($date_last_modified != $modified){
						// update file in db
						
						/ *mysqli_query($conn, "
							insert into t_file_log
							(
								id_file,
								id_host,
								date_log,
								text_log,
								
								size,
								version,
								date_last_modified
							)
							values
							(
								" . $dbfile['id_file'] . ",
								" . $id_host . ",
								now(),
								'File uploaded" . "',
								
								" . $filesize . ",
								" . ($dbfile['version'] + 1) . ",
								'" . date('Y-m-d H:i:s', $modified) . "'
							)
							");* /
							
						mysqli_query($conn, "
							update t_file 
							set
								size = " . $filesize . ",
								version = version + 1,
								date_last_modified = '" . date('Y-m-d H:i:s', $modified) . "'
							where
								id_file = " . $dbfile['id_file'] . "
							");
							
					}
					else {
						/ *mysqli_query($conn, "
							insert into t_file_log
							(
								id_file,
								id_host,
								date_log,
								text_log,
								
								size,
								version,
								date_last_modified
							)
							values
							(
								" . $dbfile['id_file'] . ",
								" . $id_host . ",
								now(),
								'File uploaded, but no changes',
								
								" . $dbfile['size'] . ",
								" . $dbfile['version'] . ",
								'" . date('Y-m-d H:i:s', $dbfile['modified']) . "'
							)
							");* /
					}
					break;
				}
			}
			
			// not in DB yet, insert
			if($file_found == 0){
				
				mysqli_query($conn, "
					insert into t_file
					(
						id_share,
						filename ,
						relative_directory,
						size,
						version,
						date_last_modified
					)
					values
					(
						" . $id_share . ",
						'" . mysqli_real_escape_string($conn, $filename) . "',
						'" . mysqli_real_escape_string($conn, $reldir) . "',
						" . $filesize . ",
						1,
						'" . date('Y-m-d H:i:s', $modified) . "'
					)
					");
				$new_id_file = mysqli_insert_id($conn);
				
				/ *mysqli_query($conn, "
					insert into t_file_log
					(
						id_file,
						id_host,
						date_log,
						text_log,
						
						size,
						version,
						date_last_modified
					)
					values
					(
						" . $new_id_file . ",
						" . $id_host . ",
						now(),
						'File uploaded, new',
						
						" . $filesize . ",
						1,
						'" . date('Y-m-d H:i:s', $modified) . "'
					)
					");* /
			}
			else {
			
			}
			*/
		}
        die('<result>' . $chunk . '</result>');
    }
	else {
		die('<error>could not upload</error>');
	}
}
else {
    die('<error>' . implode(' ', $errors) . '</error>');
}

?>