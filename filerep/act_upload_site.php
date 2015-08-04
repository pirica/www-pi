<?php

//ini_set('upload_tmp_dir', '/media/usbdrive/_filerep_temp/');
/*
echo ini_get('upload_tmp_dir');
echo ini_get('upload_max_filesize');
echo ini_get('max_file_uploads');
*/

//$dir = str_replace("'", "\'", saneInput('dir', 'string', '/'));

//$temppath = '.filerep' . ((int)(rand() * 999999999)) ;

if(isset($_FILES["myfile"]))
{
	$ret = array();
	$error = $_FILES["myfile"]["error"];
	
	if(!is_array($_FILES["myfile"]['name'])) //single file
	{
		$fileName = $_FILES["myfile"]["name"];
		$ret[$fileName] = $dir . $fileName;
		
		if(file_exists($dir . $fileName)){
			$ret['jquery-upload-file-error'] = 'File '.$fileName.' already exists!';
		}
		else 
		{
			move_uploaded_file($_FILES["myfile"]["tmp_name"], $dir . $fileName);
		}
	}
	else
	{
		$fileCount = count($_FILES["myfile"]['name']);
		for($i=0; $i < $fileCount; $i++)
		{
			$fileName = $_FILES["myfile"]["name"][$i];
			$ret[$fileName] = $dir . $fileName;
			
			if(file_exists($dir . $fileName)){
				$ret['jquery-upload-file-error'] = 'File '.$fileName.' already exists!';
			}
			else 
			{
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $dir . $fileName );
			}
		}
	}

	echo json_encode($ret);

}

?>