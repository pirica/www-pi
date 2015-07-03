<?php

//ini_set('upload_tmp_dir', '/media/usbdrive/_filerep_temp/');
/*
echo ini_get('upload_tmp_dir');
echo ini_get('upload_max_filesize');
echo ini_get('max_file_uploads');
*/

$dir = str_replace("'", "\'", saneInput('dir', 'string', '/'));

$temppath = '.filerep' . ((int)(rand() * 999999999)) ;

if(isset($_FILES["myfile"]))
{
	$ret = array();

	$error =$_FILES["myfile"]["error"];
   
    
	if(!is_array($_FILES["myfile"]['name'])) //single file
	{
		$fileName = $_FILES["myfile"]["name"];
		move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir. $_FILES["myfile"]["name"]);
		 //echo "<br> Error: ".$_FILES["myfile"]["error"];
		 
			 $ret[$fileName]= $output_dir.$fileName;
	}
	else
	{
			$fileCount = count($_FILES["myfile"]['name']);
		  for($i=0; $i < $fileCount; $i++)
		  {
			$fileName = $_FILES["myfile"]["name"][$i];
			 $ret[$fileName]= $output_dir.$fileName;
			move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName );
		  }
	
	}
    
    echo json_encode($ret);
 
}

?>