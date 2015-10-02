<?php
include 'connection.php';
include 'functions.php';
include 'act_settings.php';

require '../_core/appinit.php';

$id_share = saneInput('id_share', 'int', -1);
$id_host = saneInput('id_host', 'int', -1);
$id_file = saneInput('id_file', 'int', -1);
$dir = str_replace("'", "\'", saneInput('dir', 'string', '/'));

$app->setHeaderScripts('<script type="text/javascript">var id_share = ' . $id_share . ', id_host = ' . $id_host . ', id_file = ' . $id_file . ', dir = \'' . $dir . '\';</script>' . "\n");

switch($action->getCode()){
	
	/*
	case 'login':
		include '../_core/dsp_header.php';
		include '../users/dsp_loginform.php';
		include '../_core/dsp_footer.php';
		break;
	*/
	
	// main: overview
	case 'details':
		$show_all = saneInput('all', 'int', $settings->val('details_showall_default_value', 0));
		
		$sort = strtolower(saneInput('sort'));
		$sortorder = strtolower(saneInput('sortorder'));
		$search = strtolower(saneInput('search'));

		// invalid values : reset to default
		if(!in_array($sort, array('filename', 'size', 'date_last_modified'))){
			$sort = $settings->val('detailgrid_default_sort', 'filename');
		}
		if(!in_array($sortorder, array('asc', 'desc'))){
			$sortorder = $settings->val('detailgrid_default_sortorder', 'desc');
		}
		
		include 'queries/pr_get_files.php';
		
		// row 1: current directory
		$currentdir = mysql_fetch_array($qry_files_currentdir);
		// row 2: parent directory
		$parentdir = mysql_fetch_array($qry_files_parentdir);
		
		$currentdirarr = explode('/', $currentdir['relative_directory']);
		
		$app->setHeaderScripts('<script type="text/javascript">var show_all = ' . $show_all . ', sort = "' . $sort . '", sortorder = "' . $sortorder . '", search = "' . $search . '";</script>' . "\n");
		
		include '../_core/dsp_header.php';
		include 'dsp_detail.php';
		include '../_core/dsp_footer.php';
        
        break;
	
	
	case 'search':
		$show_all = saneInput('all', 'int', $settings->val('details_showall_default_value', 0));
		
		$sort = strtolower(saneInput('sort'));
		$sortorder = strtolower(saneInput('sortorder'));
		$search = strtolower(saneInput('search'));

		// invalid values : reset to default
		if(!in_array($sort, array('relative_directory', 'filename', 'size', 'date_last_modified'))){
			$sort = $settings->val('detailgrid_default_sort', 'filename');
		}
		if(!in_array($sortorder, array('asc', 'desc'))){
			$sortorder = $settings->val('detailgrid_default_sortorder', 'desc');
		}
		
		if($search != ''){
			include 'queries/pr_get_files_search.php';
		}
		
		$app->setHeaderScripts('<script type="text/javascript">var show_all = ' . $show_all . ', sort = "' . $sort . '", sortorder = "' . $sortorder . '", search = "' . $search . '";</script>' . "\n");
		
		include '../_core/dsp_header.php';
		include 'dsp_search.php';
		include '../_core/dsp_footer.php';
        
        break;
		
	
	case 'do_set_directory_reindex':
		include 'queries/pr_set_directory_reindex.php';
		break;
	
		
	case 'downloadfile':
		include 'act_download_file.php';
		break;
	
	case 'viewfile':
		include 'act_view_file.php';
		break;
	
	
	case 'upload':
		
		$app->setHeaderScripts('<link href="styles/uploadfile.css" rel="stylesheet">');
		$app->setHeaderScripts('<script src="../_assets/scripts/jquery/jquery.uploadfile.js"></script>');
		
		$app->setHeaderScripts('<script type="text/javascript">var upload_max_filesize = ' . revertFileSize(ini_get('upload_max_filesize')) . ', max_file_uploads = ' . ini_get('max_file_uploads') . ';</script>' . "\n");
		
		include '../_core/dsp_header.php';
		include 'dsp_upload.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'do_upload':
		include 'queries/pr_get_share_stats.php';
		include 'act_upload_site.php';
		break;
	
	
	case 'free_upload':
		ini_set('post_max_size', '256M');
		ini_set('upload_max_filesize', '256M');
		
		$id_share = 9;
		$dir = '/uploads/';
		
		$app->setHeaderScripts('<link href="styles/uploadfile.css" rel="stylesheet">');
		$app->setHeaderScripts('<script src="../_assets/scripts/jquery/jquery.uploadfile.js"></script>');
		
		$app->setHeaderScripts('<script type="text/javascript">var upload_max_filesize = ' . revertFileSize(ini_get('upload_max_filesize')) . ', max_file_uploads = ' . ini_get('max_file_uploads') . ';</script>' . "\n");
		
		include '../_core/dsp_header_minimal.php';
		include 'dsp_free_upload.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'do_free_upload':
		ini_set('post_max_size', '256M');
		ini_set('upload_max_filesize', '256M');
		
		require '../messages/functions.php';
		include 'queries/pr_get_share_stats.php';
		include 'act_upload_site.php';
		
		if($fileCount == 1){
			$channel = 'Filerep_uploads';
			$title =  'New file uploaded';
			$msg = 'File: ' . $filename;
			$priority = $settings->val('upload_alerting_priority', 1);
			send_msg($channel, $title, $msg, $priority);
		}
		else if($fileCount > 1){
			$channel = 'Filerep_uploads';
			$title =  'New files uploaded';
			$msg = 'Files: ' . $filename;
			$priority = $settings->val('upload_alerting_priority', 1);
			send_msg($channel, $title, $msg, $priority);
		}
		break;
	
	
	
	case 'create_dir':
		$newdir = saneInput('newdir');
		$error = saneInput('error');
		include 'queries/pr_get_files.php';
		
		// row 1: current directory
		$currentdir = mysql_fetch_array($qry_files_currentdir);
		// row 2: parent directory
		$parentdir = mysql_fetch_array($qry_files_parentdir);
		
		$currentdirarr = explode('/', $currentdir['relative_directory']);
		
		include '../_core/dsp_header.php';
		include 'dsp_create_dir.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'do_create_dir':
		$newdir = saneInput('newdir');
		include 'queries/pr_get_share_stats.php';
		include 'act_create_dir.php';
		break;
	
	
	
	// main: overview
	default:
		include 'queries/pr_get_share_stats.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
}


?>