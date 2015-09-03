<?php
include 'connection.php';
require 'functions.php';

require '../_core/appinit.php';

$id_camera = saneInput('id_camera', 'int', -1);
$date = saneInput('date', 'string', '');
$time = saneInput('time', 'string', '');

$archived = 0;

switch($action->getCode()){
	/*
	case 'login':
		include '../_core/dsp_header.php';
		include '../users/dsp_loginform.php';
		include '../_core/dsp_footer.php';
		break;
	*/
	
	case 'image':
        $src = saneInput('src', 'string', '');
        header("Content-Type: image/jpeg");
        readfile($main_dir . $src);
        break;
	
	case 'video':
        $src = saneInput('src', 'string', '');
        header("Content-Type: video/mp4");
        readfile($main_dir . $src);
        break;
	
		
		
	case 'archive':
		$archived = 1;
	case 'view':
		include 'queries/pr_get_cameras.php';
		include 'queries/pr_get_camera_log_menu'.($archived == 1 ? '_archived' : '').'.php';
		if($date != ''){
			include 'queries/pr_get_camera_log'.($archived == 1 ? '_archived' : '').'.php';
		}
		include 'act_view.php';
		include 'act_camera.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_submenu.php';
		include 'dsp_view.php';
		include '../_core/dsp_footer.php';
		break;
	
	
	case 'do_archive':
		include 'queries/pr_get_camera_log.php';
		//include 'act_view.php';
		
		while($camera_log = mysql_fetch_array($qry_camera_log)){
			if($camera_log['date'] == $date && ($time == 'all' || $camera_log['hour_lbl'] == $time)){
				if (!is_dir($archive_dir . $date)) {
					mkdir($archive_dir . $date, 0777);
				}
				// move files
				rename($main_dir . $date . $camera_log['name'], $archive_dir . $date . $camera_log['name']);
			}
		}
		
		include 'queries/pr_archive_camera_log.php';
		goto_action('view', false, 'date=' . $date . '&time=' . $time . '');
		break;
	
	
	case 'do_delete':
		include 'queries/pr_get_camera_log.php';
		//include 'act_view.php';
		
		while($camera_log = mysql_fetch_array($qry_camera_log)){
			if($camera_log['date'] == $date && ($time == 'all' || $camera_log['hour_lbl'] == $time)){
				// delete files
				unlink($main_dir . $date . $camera_log['name']);
			}
		}
		
		include 'queries/pr_delete_camera_log.php';
		goto_action('view', false, 'date=' . $date . '&time=' . $time . '');
		break;
	
	
	case 'camera':
		include 'queries/pr_get_cameras.php';
		//include 'act_main.php';
		include 'act_camera.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_submenu.php';
		include 'dsp_camera.php';
		include '../_core/dsp_footer.php';
		break;
		
	
	case 'jcameras':
		include 'queries/pr_get_cameras.php';
		include 'js_cameras.php';
		break;
		
		
	// main: overview
	default:
		include 'queries/pr_get_cameras.php';
		//include 'act_main.php';
		include 'act_camera.php';
		
		include '../_core/dsp_header.php';
		//include 'dsp_submenu.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
	
}
?>