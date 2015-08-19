<?php

set_time_limit(0);
ini_set('max_input_time', 99999);

include 'connection.php';
include 'act_settings.php';
include 'functions.php';
require dirname(__FILE__).'/../_core/functions.php';



// check if script is already running - no, continue
if($setting_fileindex_running == '0' && $setting_directoryindex_running == '0' && $setting_shareindex_running == '0'){
	// mark as running
	mysql_query("update t_setting set value = '1' where code = 'fileindex_running'", $conn);

	//echo "Build index started on " . date('Y-m-d H:i:s', time()) . "'\n";
	
	flush();
	
	$qry_shares = mysql_query("
		select
			d.id_directory,
			d.relative_directory,
			d.date_last_checked,
			d.active,
			s.id_share,
			s.name,
			s.server_directory
			
		from t_directory d
		join t_share s on s.id_share = d.id_share
			and s.active = 1
		
		where
			d.date_last_checked is null
			#and d.active = 1
			
		#order by 
		#	ifnull(d.date_last_checked, '2000-01-01 00:00:00'),
		#	d.relative_directory
			
		limit 20
		
		", $conn);
		

	$id_share = -1;

	while ($share = mysql_fetch_array($qry_shares)) {
		$id_share = $share{'id_share'};
		//$dir = $share{'server_directory'};
		$dir = $share{'server_directory'} . $share{'relative_directory'};
		
		/*
		$date_last_replicated = $share{'date_last_replicated'};
		
		// make db timestamp into unix time
		$date_last_replicated = str_replace('-', '/', $date_last_replicated);
		$date_last_replicated = explode('.', $date_last_replicated)[0];
		$date_last_replicated = strtotime($date_last_replicated);
		
		if($date_last_replicated == '' || $date_last_replicated == null){
			$date_last_replicated = 0;
		}
		*/
		
		$date_start = time();
		
		
		//echo "Share '" . $share{'name'} . "' (" . $share{'server_directory'} . ") (check since " . $share{'date_last_replicated'} . ")\n";
		//echo " -> started on " . date('Y-m-d H:i:s', time()) . "'\n";
		//echo " -> modified since " . date('Y-m-d H:i:s', $date_last_replicated) . "'\n";
		//echo " -> modified since " . $share{'date_last_replicated'} . "'\n";
			
		flush();
		
		$is_dir = true;
		try {
			$is_dir = is_dir($dir);
		}
		catch(Exception $e){}
		
		if ($is_dir !== true || $share['active'] == 0) {
			
			// set directory inactive
			mysql_query("
				update t_directory
				set
					active = 0,
					date_deleted = now()
				where
					id_directory = " . $share['id_directory'] . "
					and active = 1
				", $conn);
			
			// set related files inactive
			mysql_query("
				update t_file
				set
					active = 0,
					date_deleted = now()
				where
					f.id_share = " . $id_share . "
					and relative_directory = '" . mysql_real_escape_string($share{'relative_directory'}) . "'
					and active = 1
				", $conn);
			
		}
		else {
			
			// get files per share (that already exist of cource)
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
					f.id_share = " . $id_share . "
					#and replace(f.relative_directory, f.filename, '') = '" . mysql_real_escape_string($share{'relative_directory'}) . "'
					and f.relative_directory = '" . mysql_real_escape_string($share{'relative_directory'}) . "'
				", $conn);
			
			echo " -> " . mysql_num_rows($qry_files) . " files for directory '" . $dir . "' in DB\n";
			flush();
			
			// get files in the share directory
			$files = [];
			//$filesfound = list_dir_shell($files, $dir, $date_last_replicated, 0);
			$filesfound = list_dir_shell($files, $dir, 0, 0, 0, 0);
			
			//print_r($files);
			
			if($filesfound){
				
				if(count($files) > 0){
					echo " -> " . count($files) . " files on disk\n";
				}
				flush();
				
				
				$modifiedcount = 0;
				
				while ($dbfile = mysql_fetch_array($qry_files)) {
					
					$file_found = 0;
					$file_valid = 0;
					
					// make db timestamp into unix time
					$date_last_modified = $dbfile['date_last_modified'];
					$date_last_modified = str_replace('-', '/', $date_last_modified);
					$date_last_modified = explode('.', $date_last_modified)[0];
					$date_last_modified = strtotime($date_last_modified);
					
					$filecount = count($files);
					for ($i = 0; $i < $filecount; $i++) {
						
						//$extension = substr( $files[$i]['nativepath'], strrpos( $files[$i]['nativepath'], '.') );
						$extarr = explode('.', $files[$i]['nativepath']);
						$extension = '.' . $extarr[count($extarr) - 1];
						
						// file marked as deleted, ignore
						if($extension == '.deleted'){
							$file_found = 3;
							break;
						}
						// file is being uploaded, ignore
						else if(substr($extension, 0, 8) == '.filerep'){
							$file_found = 2;
							
							// if modification date some time in the past, delete (faulty upload)
							// default: more than 1 day old
							if(//$share{'id_share'} == 6 &&
							$files[$i]['modified_cest'] < time() - (3600 * 24)){
								echo " -> deleting temporary upload file " . $files[$i]['nativepath'] . "\n";
								//unlink($files[$i]['nativepath']);
							}
							
							break;
						}
						
						// file found in db
						else if($share{'server_directory'} . $dbfile['relative_directory'] . $dbfile['filename'] == $files[$i]['nativepath']){
							$file_found = 1;
							
							// file deleted, but not yet on server
							if($dbfile['active'] == 0){
								$file_found = 4;
								//if($share{'id_share'} == 6){
									echo " -> deleting file " . $files[$i]['nativepath'] . "\n";
									//shell_exec('mv "' . $files[$i]['nativepath'] . '" "' . $files[$i]['nativepath'] . '.deleted"');
								//}
								break;
							}
							// file is modified
							else if($date_last_modified != $files[$i]['modified_cest']){
								// update file in db
								
								$file_valid = 1;
								
								$modifiedcount++;
								$modlog = 'old modification date: ' . $date_last_modified . ', new modification date: ' . $files[$i]['modified_cest'];
								
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
										" . $setting_server_id_host . ",
										now(),
										'File modified on server: " . mysql_real_escape_string($modlog) . "',
										
										" . $files[$i]['size'] . ",
										" . ($dbfile['version'] + 1) . ",
										'" . date('Y-m-d H:i:s', $files[$i]['modified_cest']) . "'
									)
									", $conn);*/
									
								mysql_query("
									update t_file 
									set
										size = " . $files[$i]['size'] . ",
										version = version + 1,
										date_last_modified = '" . date('Y-m-d H:i:s', $files[$i]['modified_cest']) . "'
									where
										id_file = " . $dbfile['id_file'] . "
									", $conn);
									
							}
							else {
								$file_valid = 2;
							}
							break;
						}
						
					}
					
					// file found in db:
					if($file_found > 0){
						// remove from files list 
						unset($files[$i]);
						$files = array_values($files);
						
						if($file_found == 1){
							// and mark as checked
							mysql_query("
								update t_file 
								set date_last_checked = now(), active = 1
								where
									id_file = " . $dbfile['id_file'] . "
								", $conn);
						}
					}
					// file not found after index was reset (all files checked on dir)
					else if($file_valid == 0 && $dbfile['active'] == 1 /*&& $date_last_replicated == 0*/){
						// check deleted
						/*mysql_query("
							update t_file 
							set
								date_deleted_check = now()
								#active = 0
							where
								id_file = " . $dbfile['id_file'] . "
								and date_deleted_check is not null
							", $conn);
						*/
						
						// mark as deleted
						mysql_query("
							update t_file 
							set
								date_deleted = now(),
								active = 0
							where
								id_file = " . $dbfile['id_file'] . "
							", $conn);
						
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
								" . $setting_server_id_host . ",
								now(),
								'File deleted on server',
								
								" . $files[$i]['size'] . ",
								" . ($dbfile['version']) . ",
								'" . date('Y-m-d H:i:s', $files[$i]['modified_cest']) . "'
							)
							", $conn);*/
						
					}
					
				}
				
				if($modifiedcount > 0){
					echo " -> " . $modifiedcount . " files updated\n";
				}
				
				// files not in db: add
				$filecount = count($files);
				
				if($filecount > 0){
					echo " -> " . $filecount . " new files found on disk\n";
				}
				
				flush();
				
				for ($i = 0; $i < $filecount; $i++) {
					
					$reldir = $files[$i]['nativepath'];
					//$reldir = str_replace($dir, '', $reldir, 1);
					
					// remove server dir - only first instance
					//$reldir = preg_replace($dir, '', $reldir, 1);
					$reldir = implode('', explode($share{'server_directory'}, $reldir, 2));
					$filenamearr = explode('/', $reldir);
					array_pop($filenamearr);
					$reldir = implode('/', $filenamearr) . '/';
					
					// remove file name - only last instance
					//$reldir = strrev(implode(strrev(''), explode($files[$i]['name'], strrev($reldir), 2)));
					
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
							'" . mysql_real_escape_string($files[$i]['name']) . "',
							'" . mysql_real_escape_string($reldir) . "',
							" . $files[$i]['size'] . ",
							1,
							'" . date('Y-m-d H:i:s', $files[$i]['modified_cest']) . "'
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
							" . $setting_server_id_host . ",
							now(),
							'File located on server',
							
							" . $files[$i]['size'] . ",
							1,
							'" . date('Y-m-d H:i:s', $files[$i]['modified_cest']) . "'
						)
						", $conn);*/
				}
				
				// set date last replicated on share
				mysql_query("
					update t_host_share
					set
						date_last_replicated = '" . date('Y-m-d H:i:s', $date_start) . "'
					where
						active = 1
						and id_share = " . $id_share . "
						and id_host = " . $setting_server_id_host . " 
					", $conn);
				
				//echo "Share '" . $share{'name'} . "' done\n";
				echo " -> finished on " . date('Y-m-d H:i:s', time()) . "'\n";
				
			}
			// $filesfound = false
			else {
				echo ' -> ERROR: could not read directory "' . $dir . '" on ' . date('Y-m-d H:i:s', time()) . "'\n";
				
			}
			echo "\n";
			
			// set date last replicated on share
			mysql_query("
				update t_directory
				set
					date_last_checked = '" . date('Y-m-d H:i:s', $date_start) . "'
				where
					id_directory = " . $share['id_directory'] . "
				", $conn);
			
		}
	}
	
	// script is done, unmark as running
	mysql_query("update t_setting set value = '0' where code = 'fileindex_running'", $conn);
	
}

?>