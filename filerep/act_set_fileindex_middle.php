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
			'" . mysql_real_escape_string($path) . "',
			'" . mysql_real_escape_string($files[$i]->n) . "',
			'" . mysql_real_escape_string($files[$i]->m) . "',
			" . mysql_real_escape_string($files[$i]->c) . ",
			" . mysql_real_escape_string($files[$i]->e) . ",
		
			" . $id_share . ",
			" . $id_host . " 
		)
		";
	
	if($counter == 10){
		mysql_query("
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
			, $conn);
		
		$counter = 0;
		$sql = "";
	}
	$insertcount++;
	
	
}

if($sql != ""){
	mysql_query("
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
		, $conn);
	
}


$logging = $logging . ' mod:' . $modifiedcount;
$logging = $logging . ' ins:' . $insertcount;

$returnvalue = array('data' => [], 'logging' => $logging);


?>