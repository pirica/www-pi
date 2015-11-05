<?php
$fulldir = '';
if(isset($_GET['d'])){
	$fulldir = $_GET['d'];
}
$data = array();

if($fulldir != ''){
	
	$handle = opendir($fulldir);

	while (($file = readdir($handle)) == true){
		$fullfile = $fulldir . $file;
		if($file != '.' && $file != '..'){
			if(is_dir($fullfile)){
				$size = -1;
				$modified = '';
				$dir = 1;
			}
			else {
				$size = filesize($fullfile);
				$modified = filemtime($fullfile);
				$dir = 0;
			}
			
			$data[] = array(
				'name' => $file,
				'nativepath' => $fullfile,
				'size' => $size,
				'modified' => $modified,
				'dir' => $dir
			);
			
		}
	}

	closedir( $handle ); 

}

echo json_encode($data);

?>