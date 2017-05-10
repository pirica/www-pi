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
	
	case 'setup':
		shell_exec('if [ ! -d /var/www/html/lego/thumbs ]; then ln -s ' . $settings->val('manuals_directory_thumbs', '') . ' /var/www/html/lego/thumbs > /dev/null 2>&1; fi');
		break;
	
	case 'view':
		$app->setTitle($set['name'] . ' (' . $set['set_num'] . ')');
		
		$files = array();
		if($setId != '')
		{
			$fulldir = $settings->val('manuals_directory', '') . $setId;
			if(is_dir($fulldir))
			{
				list_dir($files, $fulldir, 0, 1, 0);
				usort($files, "arraysort_compare");
			}
		}
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_view.php';
		require '../_core/dsp_footer.php';
		break;
	
	// main: overview
	default:
		$app->setTitle($map);
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_main.php';
		require '../_core/dsp_footer.php';
	
	
}
?>