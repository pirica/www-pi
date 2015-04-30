<?php
include 'connection.php';
include 'functions.php';
include 'act_settings.php';

require '../_core/appinit.php';

$id_share = saneInput('id_share', 'int', -1);
$id_host = saneInput('id_host', 'int', -1);
$id_file = saneInput('id_file', 'int', -1);

$app->setHeaderScripts('<script type="text/javascript">var id_share = ' . $id_share . ', id_host = ' . $id_host . ', id_file = ' . $id_file . ';</script>' . "\n");

switch($action->getCode()){

	case 'login':
		include '../_core/dsp_header.php';
		include '../users/dsp_loginform.php';
		include '../_core/dsp_footer.php';
		break;
	
	// main: overview
	case 'details':
		$dir = str_replace("'", "\'", saneInput('dir', 'string', '/'));
		
		include 'queries/pr_get_files.php';
		
		// row 1: current directory
		$currentdir = mysql_fetch_array($qry_files_currentdir);
		// row 2: parent directory
		$parentdir = mysql_fetch_array($qry_files_parentdir);
		
		$currentdirarr = explode('/', $currentdir['relative_directory']);
		
		include '../_core/dsp_header.php';
		include 'dsp_detail.php';
		include '../_core/dsp_footer.php';
        
        break;
	
	// main: overview
	default:
		include 'queries/pr_get_share_stats.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
}


?>