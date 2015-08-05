<?php

//ini_set('upload_tmp_dir', '/media/usbdrive/_filerep_temp/');
/*
echo ini_get('upload_tmp_dir');
echo ini_get('upload_max_filesize');
echo ini_get('max_file_uploads');
*/

//$dir = str_replace("'", "\'", saneInput('dir', 'string', '/'));

//$temppath = '.filerep' . ((int)(rand() * 999999999)) ;

$server_directory = '';

while($stat = mysql_fetch_array($qry_share_stats)){
	if($stat['id_share'] == $id_share){
		$server_directory = $stat['server_directory'];
	}
}

if(isset($_FILES["myfile"]))
{
	$ret = array();
	$error = $_FILES["myfile"]["error"];
	
	if(!is_array($_FILES["myfile"]['name'])) //single file
	{
		$fileName = $_FILES["myfile"]["name"];
		$ret[$fileName] = $server_directory . $dir . $fileName;
		
		if(file_exists($server_directory . $dir . $fileName)){
			$ret['jquery-upload-file-error'] = 'File '.$fileName.' already exists!';
		}
		else 
		{
			move_uploaded_file($_FILES["myfile"]["tmp_name"], $server_directory . $dir . $fileName);
			
			$filesize = filesize($server_directory . $dir . $fileName);
			$modified = filemtime($server_directory . $dir . $fileName);
			
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
			//$new_id_file = mysql_insert_id($conn);
			
		}
	}
	else
	{
		$fileCount = count($_FILES["myfile"]['name']);
		for($i=0; $i < $fileCount; $i++)
		{
			$fileName = $_FILES["myfile"]["name"][$i];
			$ret[$fileName] = $server_directory . $dir . $fileName;
			
			if(file_exists($server_directory . $dir . $fileName)){
				$ret['jquery-upload-file-error'] = 'File '.$fileName.' already exists!';
			}
			else 
			{
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $server_directory . $dir . $fileName );
				
				$filesize = filesize($server_directory . $dir . $fileName);
				$modified = filemtime($server_directory . $dir . $fileName);
				
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
				//$new_id_file = mysql_insert_id($conn);
				
			}
		}
	}

	echo json_encode($ret);

}

?>