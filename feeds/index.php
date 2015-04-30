<?php
include 'connections.php';
//include 'functions.php';

require '../_core/appinit.php';

$id_feed = saneInput('id_feed', 'int', -1);
$id_feed_entry = saneInput('id_feed_entry', 'int', -1);
$ajaxcall = saneInput('ajaxcall', 'bool', false);
$feed_entries = saneInput('entries', 'string');

$desktop = 1;
if(isset($_SESSION['desktop'])){
	$desktop = $_SESSION['desktop'];
}


switch($action->getCode()){
	
	
	case 'login':
		include '../_core/dsp_header.php';
		include '../users/dsp_loginform.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'feeds':
		include 'queries/pr_feeds.php';
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_feeds.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'setfeed':
		include 'queries/pr_feeds.php';
		include 'act_init_feed.php';
		
		$error = 0;
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_edit.php';
		require '../_core/dsp_footer.php';
		
		break;
	
	case 'do_setfeed':
		include 'act_set_feed.php';
		goto_action('feeds', false, 'id_feed=' . $id_feed);
		break;
	
	
	
	
	case 'delfeed':
		$ajaxcall = saneInput('ajaxcall', 'boolean', true);
		
		include 'queries/pr_feeds.php';
		
		include 'act_init_feed.php';
		
		if($ajaxcall === false){
			require '../_core/dsp_header.php';
		}
		include 'dsp_delete.php';
		if($ajaxcall === false){
			require '../_core/dsp_footer.php';
		}
		break;
	
	case 'do_delfeed':
		include 'act_del_feed.php';
		goto_action('feeds', false, 'id_feed=' . $id_feed);
		break;
	
	
	
	
	// feed overviews + details
	
	case 'details':
		$app->setTitle('Details');
		
		include 'queries/pr_feeds.php';
		include 'act_init_feed.php';
		
		include 'act_init_feed_detail.php';
		
		
		//include 'queries/pr_feed_files.php';
		
		require '../_core/dsp_header.php';
		include 'dsp_detail.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	
	case 'entries':
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 1 Sep 2014 00:00:00 GMT"); // Date in the past
		
		include 'queries/pr_feed_entries.php';
		
		if($ajaxcall === false){
			require '../_core/dsp_header.php';
		}
		include 'dsp_feed_entries.php';
		if($ajaxcall === false){
			require '../_core/dsp_footer.php';
		}
		break;
	
	
	case 'setfeedentry':
		$is_read = saneInput('is_read', 'int', -1);
		
		if($feed_entries == '' && $id_feed_entry > 0){
			$feed_entries = $id_feed_entry;
			echo 'entries=id';
		}
		else {
			// check if numerical and comma separated, and if it begins and ends with a number
			if(!preg_match('/^\d+(?:,\d+)*$/', $feed_entries)){
				$feed_entries = '-1';
				echo 'entries=empty';
			}
		}
		
		if($is_read == 1){
			mysql_query("
				update t_feed_entry
				set
					is_read = 1,
					date_read = now(),
					date_modified = now()
				
				where
					id_feed_entry in (" . $feed_entries . ")
					and ifnull(is_read,0) = 0
					
				", $conn) or die(mysql_error());
		}
		else if($is_read == 0){
			mysql_query("
				update t_feed_entry
				set
					is_read = 0,
					date_modified = now()
				
				where
					id_feed_entry in (" . $feed_entries . ")
					
				", $conn) or die(mysql_error());
		}
		break;
	
	
	// main: overview
	default:
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 1 Sep 2014 00:00:00 GMT"); // Date in the past
		
		include 'queries/pr_feed_overview.php';
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_main.php';
		require '../_core/dsp_footer.php';
	
	
}
?>