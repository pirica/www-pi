<?php

$server_directory = '';

while($shares = mysql_fetch_array($qry_shares)){
	if($shares['id_share'] == $id_share){
		$server_directory = $shares['server_directory'];
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
				insert into t_directory
				(
					id_share,
					dirname ,
					relative_directory,
					parent_directory,
					date_last_modified
				)
				values
				(
					" . $id_share . ",
					'" . mysql_real_escape_string($newdir) . "',
					'" . mysql_real_escape_string($dir . $newdir . '/') . "',
					'" . mysql_real_escape_string($dir) . "',
					'" . date('Y-m-d H:i:s', $modified) . "'
				)
				", $conn);
			
			goto_action('details', false, 'id_share=' . $id_share . '&dir=' . $dir . $newdir . '/');
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