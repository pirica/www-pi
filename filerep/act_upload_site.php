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

$fileCount = 0;
$filenames = '';

if(isset($_FILES["myfile"]))
{
	$ret = array();
	$error = $_FILES["myfile"]["error"];
	
	$fileCount = 1;
	
	if(!is_array($_FILES["myfile"]['name'])) //single file
	{
		$filename = $_FILES["myfile"]["name"];
		$ret[$filename] = $server_directory . $dir . $filename;
		
		if(file_exists($server_directory . $dir . $filename)){
			$ret['jquery-upload-file-error'] = 'File '.$filename.' already exists!';
		}
		else if($_FILES['myfile']['error'] == 0)
		{
			move_uploaded_file($_FILES["myfile"]["tmp_name"], $server_directory . $dir . $filename);
			
			$filesize = filesize($server_directory . $dir . $filename);
			$modified = filemtime($server_directory . $dir . $filename);
			
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
		else {
			switch ($_FILES['myfile']['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$ret['jquery-upload-file-error'] = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$ret['jquery-upload-file-error'] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
					break;
				case UPLOAD_ERR_PARTIAL:
					$ret['jquery-upload-file-error'] = "The uploaded file was only partially uploaded";
					break;
				case UPLOAD_ERR_NO_FILE:
					$ret['jquery-upload-file-error'] = "No file was uploaded";
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$ret['jquery-upload-file-error'] = "Missing a temporary folder";
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$ret['jquery-upload-file-error'] = "Failed to write file to disk";
					break;
				case UPLOAD_ERR_EXTENSION:
					$ret['jquery-upload-file-error']= "File upload stopped by extension";
					break;

				default:
					$ret['jquery-upload-file-error'] = "Unknown upload error";
					break;
			} 
		}
	}
	else
	{
		$fileCount = count($_FILES["myfile"]['name']);
		for($i=0; $i < $fileCount; $i++)
		{
			$filename = $_FILES["myfile"]["name"][$i];
			$ret[$filename] = $server_directory . $dir . $filename;
			
			$filenames .= ($filenames == '' ? '' : ', ') . $filename;
			
			if(file_exists($server_directory . $dir . $filename)){
				$ret['jquery-upload-file-error'] = 'File '.$filename.' already exists!';
			}
			else if($_FILES["myfile"]["error"][$i] == 0)
			{
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $server_directory . $dir . $filename );
				
				$filesize = filesize($server_directory . $dir . $filename);
				$modified = filemtime($server_directory . $dir . $filename);
				
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
			else {
				switch ($_FILES['myfile']['error']) {
					case UPLOAD_ERR_INI_SIZE:
						$ret['jquery-upload-file-error'] = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
						break;
					case UPLOAD_ERR_FORM_SIZE:
						$ret['jquery-upload-file-error'] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
						break;
					case UPLOAD_ERR_PARTIAL:
						$ret['jquery-upload-file-error'] = "The uploaded file was only partially uploaded";
						break;
					case UPLOAD_ERR_NO_FILE:
						$ret['jquery-upload-file-error'] = "No file was uploaded";
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						$ret['jquery-upload-file-error'] = "Missing a temporary folder";
						break;
					case UPLOAD_ERR_CANT_WRITE:
						$ret['jquery-upload-file-error'] = "Failed to write file to disk";
						break;
					case UPLOAD_ERR_EXTENSION:
						$ret['jquery-upload-file-error']= "File upload stopped by extension";
						break;

					default:
						$ret['jquery-upload-file-error'] = "Unknown upload error";
						break;
				} 
			}
		}
	}

	echo json_encode($ret);

}

?>