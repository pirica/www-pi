<?php
require '../_core/webinit.php';

include 'connections.php';
include 'functions.php';

$map = saneInput('map', 'string', '');
$themeId = saneInput('themeId', 'string', '');
$setId = saneInput('setId', 'string', '');

include 'queries/qry_themes.php';
include 'queries/qry_sets.php';

switch($action->getCode()){
	
	// main: overview
	default:
		$app->setTitle($map);
		
		$fulldir = $settings->val('manuals_directory', '');
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