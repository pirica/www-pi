<?php

$newdir = '';

if(isset($_POST['newdir']) && $_POST['newdir'] != ''){
	
	$newdir = $_POST['newdir'];
	
	$is_dir = true;
	try {
		$is_dir = is_dir($newdir);
	}
	catch(Exception $e){}
	
	if($is_dir !== true){
		mkdir($dir . $newdir);
	}
	
}

?>