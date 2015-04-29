<?php
set_time_limit(0);

// Read the PHP input stream and save the contents to $rawPost.
$rawPost = file_get_contents('php://input');

//$path = $_GET['path'];
//$chunk = !empty($_GET['chunk']) ? $_GET['chunk'] : null;
//$last = isset($_GET['last']) && (strtolower('' . $_GET['last']) == 'true' || '' . $_GET['last'] == '1');
//$fileId = isset($_GET['id']) ? $_GET['id'] : '';
//$modified = isset($_GET['modified']) && $_GET['modified'] != '' && $_GET['modified'] > 0 ? $_GET['modified'] : 0;
$path = saneInput('path');
$chunk = saneInput('chunk', 'int');
$last = saneInput('last', 'bit', false);
$fileId = saneInput('id');
$modified = saneInput('modified', 'int', 0);

$errors = array();

if(empty($rawPost)) $errors[] = 'You did not send any POST data.';
if(empty($path)) $errors[] = 'You did not specify a file name in the url.';

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
			
			$qry_shares = mysql_query("
				select
					s.id_share,
					s.name,
					s.info,
					s.server_directory
				from t_share s
				where
					s.id_share = " . $id_share . "
				", $conn);
			$share = mysql_fetch_array($qry_shares);
			$dir = $share['server_directory'];
			
			$reldir = $path;
			
			// remove server dir - only first instance
			$reldir = implode('', explode($dir, $reldir, 2));
			//$filename = implode('', explode('/', $reldir));
			
			$filenamearr = explode('/', $reldir);
			//$filename = $filenamearr[count($filenamearr) - 1];
			$filename = array_pop($filenamearr);
			$reldir = implode('/', $filenamearr);
			
			$qry_files = mysql_query("
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
					f.relative_directory = '" . mysql_real_escape_string($reldir) . "'
					and f.filename = '" . mysql_real_escape_string($filename) . "'
					and f.active = 1
				", $conn);
			
			$file_found = 0;
			
			$filesize = filesize($path);
			
			//TODO: remove filename from relative_directory
			
			while ($dbfile = mysql_fetch_array($qry_files)) {
				if($reldir == $dbfile['relative_directory'] && $filename == $dbfile['filename']){
					$file_found = 1;
					
					// make db timestamp into unix time
					$date_last_modified = $dbfile['date_last_modified'];
					$date_last_modified = str_replace('-', '/', $date_last_modified);
					$date_last_modified = explode('.', $date_last_modified)[0];
					$date_last_modified = strtotime($date_last_modified . ' ' . timezone_offset_string());
					
					if($date_last_modified != $modified){
						// update file in db
						
						/*mysql_query("
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
							", $conn);*/
							
						mysql_query("
							update t_file 
							set
								size = " . $filesize . ",
								version = version + 1,
								date_last_modified = '" . date('Y-m-d H:i:s', $modified) . "'
							where
								id_file = " . $dbfile['id_file'] . "
							", $conn);
							
					}
					else {
						/*mysql_query("
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
							", $conn);*/
					}
					break;
				}
			}
			
			// not in DB yet, insert
			if($file_found == 0){
				
				mysql_query("
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
						'" . mysql_real_escape_string($filename) . "',
						'" . mysql_real_escape_string($reldir) . "',
						" . $filesize . ",
						1,
						'" . date('Y-m-d H:i:s', $modified) . "'
					)
					", $conn);
				$new_id_file = mysql_insert_id($conn);
				
				/*mysql_query("
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
					", $conn);*/
			}
			else {
			
			}
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