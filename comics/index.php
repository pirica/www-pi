<?php
include 'connections.php';
include 'functions.php';

require '../_core/webinit.php';

$comic = saneInput('comic', 'string', '');

switch($action->getCode()){
	
	case 'comic':
		
		$comics = array();
		if($comic != '')
		{
			$fulldir = $settings->val('comics_path', '') . $comic;
			if(is_dir($fulldir))
			{
				list_dir($comics, $fulldir, 0, 1, 0);
				usort($comics, "arraysort_compare");
			}
		}
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_comic.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	// main: overview
	default:
		
		$fulldir = $settings->val('comics_path', '');
		if($comic != '')
		{
			$fulldir .= $comic;
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