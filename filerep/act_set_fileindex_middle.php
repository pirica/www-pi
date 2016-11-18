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

$counter = 0;
$sql = "";

$filelen = -1;
$query_success = true;

if($files !== null){

	$filelen = count($files);
	for ($i = 0; $i < $filelen; $i++) {
		
		$path = $files[$i]->p;
		if(substr($path, -1, 1) != '/'){
			$path = $path . '/';
		}
		
		$counter++;
		
		$sql .= ($sql == '' ? '' : ',');
		$sql .= "
			(
				'" . mysqli_real_escape_string($conn, $path) . "',
				'" . mysqli_real_escape_string($conn, $files[$i]->n) . "',
				'" . mysqli_real_escape_string($conn, $files[$i]->m) . "',
				0" . /*mysqli_real_escape_string($conn, $files[$i]->c) .*/ ",
				" . mysqli_real_escape_string($conn, $files[$i]->e) . ",
			
				" . $id_share . ",
				" . $id_host . " 
			)
			";
		
		if($counter == 10){
			$query_success = $query_success && mysqli_query($conn, "
				insert into t_file_index_temp
				(
					relative_directory,
					filename,
					date_last_modified,
					conflict,
					excluded,
				
					id_share,
					id_host
				)
				values
				" . $sql
				);
			
			$counter = 0;
			$sql = "";
		}
		$insertcount++;
		
		
	}

	if($sql != ""){
		$query_success = $query_success && mysqli_query($conn, "
			insert into t_file_index_temp
			(
				relative_directory,
				filename,
				date_last_modified,
				conflict,
				excluded,
			
				id_share,
				id_host
			)
			values
			" . $sql
			);
		
	}

}

$logging = $logging . ' mod:' . $modifiedcount;
$logging = $logging . ' ins:' . $insertcount;

if($filelen > -1 && $query_success){
	$returnvalue = array('data' => [], 'logging' => $logging);
}
else {
	$returnvalue = array('type' => 'error', 'logging' => $logging);
}

?>