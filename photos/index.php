<?php
include 'connections.php';
include 'functions.php';

require '../_core/webinit.php';

$map = saneInput('map', 'string', '');

switch($action->getCode()){
	
	case 'map':
		$app->setTitle($map);
		
		$maps = array();
		if($map != '')
		{
			$fulldir = $settings->val('photos_path', '') . $map;
			if(is_dir($fulldir))
			{
				list_dir($maps, $fulldir, 0, 1, 0);
				usort($maps, "arraysort_compare");
			}
		}
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_map.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	// main: overview
	default:
		
		$fulldir = $settings->val('photos_path', '');
		if($map != '')
		{
			$fulldir .= $map;
			$app->setTitle($map);
		}
		$dirs = array();
		if(is_dir($fulldir))
		{
			list_dir($dirs, $fulldir, 0, 1, 0);
			usort($dirs, "arraysort_compare");
		}
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_main.php';
		require '../_core/dsp_footer.php';
	
	
}
?>