<?php

$cameras = [];
$cameracount = 0;

$camera = [];

while($cam = mysql_fetch_array($qry_cameras)){ 
	if($cam['id_camera'] == $id_camera){
		$camera = $cam;
		
		$app->setTitle('Camera ' . $camera['description']);
		$app->setHeaderScripts('<script type="text/javascript">var cam_address = "' . $camera['address'] . '";</script>' . "\n");
	}
	
	$cameras[] = $cam;
	$cameracount++;
}

?>