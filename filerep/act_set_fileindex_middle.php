<?php
set_time_limit(0);

$logging = '';

$filestr = saneInput('files', 'string', '[]');

$files = json_decode($filestr);

$modifiedcount = 0;
$insertcount = 0;
$conflictcount = 0;
$dpmod_newc_count = 0;
$dpmod_nonc_count = 0;
$dpmod_nonc_c_count = 0;

	
// get indexed files
$dbfiles = [];
$qry_index = mysql_query("
	select
		relative_directory,
		filename
	from t_file_index
	where
		id_share = " . $id_share . " 
		and id_host = " . $id_host . " 
	", $conn);
while ($dbfile = mysql_fetch_array($qry_index)) {
	$dbfiles[] = $dbfile;
}

$filelen = count($files);
for ($i = 0; $i < $filelen; $i++) {
	//echo $i . ': ' . $files[$i]->p . '<br/>' ;
	
	$file_found = 0;
	
	$path = $files[$i]->p;
	if(substr($path, -1, 1) != '/'){
		$path = $path . '/';
	}
	
	$dbfilelen = count($dbfiles);
	for ($j = 0; $j < $dbfilelen; $j++) {
		
		// if the file is already in the index => update
		if($dbfiles[$j]['relative_directory'] == $path && $dbfiles[$j]['filename'] == $files[$i]->n){
			$file_found = 1;
			
			if($files[$i]->c == 1){
				mysql_query("
					update t_file_index
					set
						date_previous_modified = date_last_modified
					where
						id_share = " . $id_share . " 
						and id_host = " . $id_host . " 
						and relative_directory = '" . mysql_real_escape_string($path) . "'
						and filename = '" . mysql_real_escape_string($files[$i]->n) . "'
						and conflict = 0
					", $conn);
				$dpmod_newc_count = $dpmod_newc_count + mysql_affected_rows($conn);
			}
			else {
				mysql_query("
					update t_file_index
					set
						date_previous_modified = date_last_modified
					where
						id_share = " . $id_share . " 
						and id_host = " . $id_host . " 
						and relative_directory = '" . mysql_real_escape_string($path) . "'
						and filename = '" . mysql_real_escape_string($files[$i]->n) . "'
						and conflict = 0
					", $conn);
				$dpmod_nonc_count = $dpmod_nonc_count + mysql_affected_rows($conn);
				
				mysql_query("
					update t_file_index
					set
						conflict = 0,
						date_previous_modified = date_last_modified
					where
						id_share = " . $id_share . " 
						and id_host = " . $id_host . " 
						and relative_directory = '" . mysql_real_escape_string($path) . "'
						and filename = '" . mysql_real_escape_string($files[$i]->n) . "'
						and conflict = 1
					", $conn);
				$dpmod_nonc_c_count = $dpmod_nonc_c_count + mysql_affected_rows($conn);
			}
			
			mysql_query("
				update t_file_index
				set
					date_last_modified = '" . mysql_real_escape_string($files[$i]->m) . "',
					excluded = " . mysql_real_escape_string($files[$i]->e) . ",
					notfound = 0
				
				where
					id_share = " . $id_share . " 
					and id_host = " . $id_host . " 
					and relative_directory = '" . mysql_real_escape_string($path) . "'
					and filename = '" . mysql_real_escape_string($files[$i]->n) . "'
				", $conn);
			
			$modifiedcount = $modifiedcount + mysql_affected_rows($conn);
			
			// remove from dbfiles list 
			unset($dbfiles[$j]);
			$dbfiles = array_values($dbfiles);
			
			break;
		}
	}
	
	// not found, insert
	if($file_found == 0){
		
		mysql_query("
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
			values
			(
				'" . mysql_real_escape_string($path) . "',
				'" . mysql_real_escape_string($files[$i]->n) . "',
				'" . mysql_real_escape_string($files[$i]->m) . "',
				'" . mysql_real_escape_string($files[$i]->m) . "',
				0,
				0,
				" . mysql_real_escape_string($files[$i]->e) . ",
			
				" . $id_share . ",
				" . $id_host . " 
			)
			", $conn);
			
		$insertcount++;
	}
	
}



$logging = $logging . ' mod:' . $modifiedcount;
$logging = $logging . ' ins:' . $insertcount;

$returnvalue = array('data' => [], 'logging' => $logging);


?>