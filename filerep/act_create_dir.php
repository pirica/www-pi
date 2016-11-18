<?php

$server_directory = '';

while($shares = mysqli_fetch_array($qry_shares)){
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
		
			mysqli_query($conn, "
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
					'" . mysqli_real_escape_string($conn, $newdir) . "',
					'" . mysqli_real_escape_string($conn, $dir . $newdir . '/') . "',
					'" . mysqli_real_escape_string($conn, $dir) . "',
					'" . date('Y-m-d H:i:s', $modified) . "'
				)
				");
			
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