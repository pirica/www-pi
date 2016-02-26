<?php
include 'connections.php';
include 'functions.php';

require '../_core/webinit.php';

$map = saneInput('map', 'string', '');

switch($action->getCode()){
	
	// main: overview
	default:
		$app->setTitle($map);
		
		$fulldir = $settings->val('photos_path', '');
		if($map != '')
		{
			$fulldir .= $map;
		}
		$files = array();
		if(is_dir($fulldir))
		{
			list_dir($files, $fulldir, 0, 1, 0);
			usort($files, "arraysort_compare");
		}
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_main.php';
		require '../_core/dsp_footer.php';
	
	
}
?>