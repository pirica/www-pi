<?php
require '../_core/webinit.php';

include 'connections.php';
include 'functions.php';

$id_grab = saneInput('id_grab', 'int', -1);

$app->setHeaderScripts('<script type="text/javascript">var id_grab = ' . $id_grab . ';</script>' . "\n");


switch($action->getCode()){
	/*
	case 'login':
		include '../_core/dsp_header.php';
		include '../users/dsp_loginform.php';
		include '../_core/dsp_footer.php';
		break;
	*/
	
	// actions on grab
	
	case 'start':
		$grab_enabled = 1;
		include 'queries/pr_grab_startstop.php';
		goto_action('main');
		break;
	
	case 'stop':
		$grab_enabled = 0;
		include 'queries/pr_grab_startstop.php';
		goto_action('main');
		break;
		
		
	
	case 'setgrab':
		include 'queries/pr_grabs.php';
		include 'queries/pr_grab_counts.php';
		
		include 'act_init_grab.php';
		
		$error = 0;
		
		include '../_core/dsp_header.php';
		include 'dsp_edit.php';
		include '../_core/dsp_footer.php';
		
		break;
	
	case 'do_setgrab':
		include 'act_set_grab.php';
		goto_action('main', false, 'id_grab=' . $id_grab);
		break;
	
	
	
	
	case 'delgrab':
		$ajaxcall = saneInput('ajaxcall', 'boolean', true);
		
		include 'queries/pr_grabs.php';
		
		include 'act_init_grab.php';
		
		if($ajaxcall === false){
			include '../_core/dsp_header.php';
		}
		include 'dsp_delete.php';
		if($ajaxcall === false){
			include '../_core/dsp_footer.php';
		}
		break;
	
	case 'do_delgrab':
		include 'act_del_grab.php';
		goto_action('main', false, 'id_grab=' . $id_grab);
		break;
	
	
	case 'setgrabcounter':
		include 'queries/pr_grabs.php';
		include 'queries/pr_grab_counts.php';
		
		include 'act_init_grab.php';
		include 'act_init_grab_counter.php';
		
		$error = 0;
		
		include '../_core/dsp_header.php';
		include 'dsp_edit_counter.php';
		include '../_core/dsp_footer.php';
		
		break;
	
	case 'do_setgrabcounter':
		include 'act_set_grab_counter.php';
		goto_action('setgrab', false, 'id_grab=' . $id_grab);
		break;
	
	
	
	case 'delgrabcounter':
		$ajaxcall = saneInput('ajaxcall', 'boolean', true);
		
		include 'queries/pr_grabs.php';
		include 'queries/pr_grab_counts.php';
		
		include 'act_init_grab.php';
		include 'act_init_grab_counter.php';
		
		if($ajaxcall === false){
			include '../_core/dsp_header.php';
		}
		include 'dsp_delete_counter.php';
		if($ajaxcall === false){
			include '../_core/dsp_footer.php';
		}
		break;
	
	case 'do_delgrabcounter':
		include 'act_del_grab_counter.php';
		goto_action('setgrab', false, 'id_grab=' . $id_grab);
		break;
	
	
	
	// grab overviews + details
	
	case 'details':
		include 'queries/pr_grabs.php';
		include 'act_init_grab.php';
		
		include 'act_init_grab_detail.php';
		
		include 'queries/pr_grab_files.php';
        
		$app->setHeaderScripts('<script type="text/javascript">var sort = "' . $sort . '", sortorder = "' . $sortorder . '", perpage = ' . $perpage . ', page = ' . $page . ', status = "' . $status . '", search = "' . $search . '";</script>' . "\n");
		
		include '../_core/dsp_header.php';
		include 'dsp_detail.php';
		include '../_core/dsp_footer.php';
		break;
	
	
	case 'detailsgrid':
		include 'queries/pr_grabs.php';
		
		include 'act_init_grab.php';
		include 'act_init_grab_detail.php';
		
		include 'queries/pr_grab_files.php';
		
		include 'dsp_detail_grid.php';
		break;
	
	
	case 'add_file':
		$id_grab = $settings->val('custom_downloads_id_grab',0);
		$added = saneInput('added', 'int', -1);
		if($id_grab > 0){
			include 'queries/pr_grabs.php';
			include 'act_init_grab.php';
			
			include '../_core/dsp_header.php';
			include 'dsp_add_file.php';
			include '../_core/dsp_footer.php';
		}
		else {
			goto_action('main');
		}
		break;
	
	case 'do_add_file':
		$id_grab = $settings->val('custom_downloads_id_grab',0);
		$id_grab_file = -1;
		if($id_grab > 0){
			include 'queries/pr_grabs.php';
			include 'act_init_grab.php';
			
			include 'act_add_file.php';
		}
		goto_action('add_file', false, 'added=' . ($id_grab_file > 0 ? 1 : 0));
		break;
	
	case 'js_check_file':
		$file = saneInput('file', 'string', '');
		if($file != ''){
			echo file_exists($file);
		}
		else {
			echo 'false';
		}
		break;
	
	case 'js_check_url':
		$url = saneInput('u', 'string', '');
		
		$type = '';
		$filename = '';
		
		//$ytdl = shell_exec('/usr/bin/youtube-dl --get-title ' . $url);
		$ytdl = shell_exec('/usr/bin/youtube-dl --get-filename -o "%(title)s.%(ext)s" ' . $url);
		
		if(isset($ytdl) && $ytdl != ''){
			$type = 'youtube-dl';
			$filename = $ytdl;
		}
			
		echo json_encode(array(
			"type" => $type,
			"filename" => $filename
		));
		
		break;
	
	
	case 'add_queue':
		include 'act_add_queue.php';
		break;
	
	
	case 'edit_queue':
		$id_queue = saneInput('id_queue', 'int', -1);
		$sub = saneInput('sub');
		include 'queries/pr_get_playlists.php';
		if($sub == 'confirm')
		{
			include 'queries/pr_set_queue_confirmed.php';
		}
		if($sub == 'decline')
		{
			include 'queries/pr_set_queue_declined.php';
		}
		include 'queries/pr_queue.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_edit_queue.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'do_edit_queue':
		$id_queue = saneInput('id_queue', 'int', -1);
		$filename = saneInput('filename');
		$directory = saneInput('directory');
		$playlistId = saneInput('playlistId', 'int', -1);
		
		include 'queries/pr_set_queue.php';
		break;
	
	
	// main: overview
	default:
		include 'queries/pr_grabs.php';
		
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
	
}
?>