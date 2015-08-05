<?php

$newdir = '';

$server_directory = '';

while($stat = mysql_fetch_array($qry_share_stats)){
	if($stat['id_share'] == $id_share){
		$server_directory = $stat['server_directory'];
	}
}

if(isset($_POST['newdir']) && $_POST['newdir'] != ''){
	
	$newdir = $_POST['newdir'];
	
	$is_dir = true;
	try {
		$is_dir = is_dir($server_directory . $dir . $newdir);
	}
	catch(Exception $e){}
	
	if($is_dir !== true){
		mkdir($server_directory . $dir . $newdir);
	}
	
}

?>