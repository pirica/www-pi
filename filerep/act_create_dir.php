<?php

$server_directory = '';

while($stat = mysql_fetch_array($qry_share_stats)){
	if($stat['id_share'] == $id_share){
		$server_directory = $stat['server_directory'];
	}
}

if($newdir != ''){
	
	$is_dir = true;
	try {
		$is_dir = is_dir($server_directory . $dir . $newdir);
	}
	catch(Exception $e){}
	
	if($is_dir !== true){
		
		if( mkdir($server_directory . $dir . $newdir) ){
		
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
					'" . mysql_real_escape_string($dir) . "',
					" . $filesize . ",
					1,
					'" . date('Y-m-d H:i:s', $modified) . "'
				)
				", $conn);
			
			goto_action('details', false, 'id_share=' . $id_share . '&dir=' . $dir . $newdir);
		}
		else {
			goto_action('create_dir', false, 'id_share=' . $id_share . '&dir=' . $dir . '&newdir=' . $newdir . '&error=notcreated' );
		}
	}
	else {
		goto_action('create_dir', false, 'id_share=' . $id_share . '&dir=' . $dir . '&newdir=' . $newdir . '&error=direxists' );
	}
	
}
else {
	goto_action('create_dir', false, 'id_share=' . $id_share . '&dir=' . $dir . '&newdir=' . $newdir . '&error=nodir' );
}

?>