<?php
require dirname(__FILE__).'/../_core/appinit.php';

include 'connection.php';
include 'functions.php';
//require '../_core/functions.php';

// Global method vars
$method = saneInput('method');
$id_user = saneInput('id_user', 'int', -1);
$logging = '';

// Host vars
$id_host = saneInput('id_host', 'int', -1);

// Share vars
$id_share = saneInput('id_share', 'int', -1);

// File vars
$id_file = saneInput('id_file', 'int', -1);

switch($method){
	
	/* Main functions, lookups */
	/* ======================= */
	
	case 'getStatus':
		$returnvalue = array("data" => array("status" => "ok"));
		echo json_encode($returnvalue);
		break;
	
	case 'getSettings':
		include 'act_get_settings.php';
		echo json_encode($returnvalue);
		break;
	
	case 'setSetting':
		include 'act_set_setting.php';
		echo json_encode($returnvalue);
		break;
	
	case 'login':
		include 'act_ws_login.php';
		echo json_encode($returnvalue);
		break;
	
	
	/* Hosts */
	/* ===== */
	
	case 'getHosts':
		include 'act_get_hosts.php';
		echo json_encode($returnvalue);
		break;
	
	case 'getHost':
		include 'act_get_host.php';
		echo json_encode($returnvalue);
		break;
	
	
	/* Shares */
	/* ====== */
	
	case 'getShares':
		include 'act_get_shares.php';
		echo json_encode($returnvalue);
		break;
	
	case 'setShare':
		include 'act_set_share.php';
		echo json_encode($returnvalue);
		break;
	
	case 'removeHostShare':
		include 'act_remove_host_share.php';
		echo json_encode($returnvalue);
		break;
	
	/*case 'getShareLog':
		include 'act_get_share_log.php';
		echo json_encode($returnvalue);
		break;*/
		
	case 'getShareStats':
		include 'act_get_share_stats.php';
		echo json_encode($returnvalue);
		break;
	
	
	/* Files */
	/* ===== */
	
	case 'getFileMoves':
		include 'act_get_file_moves.php';
		echo json_encode($returnvalue);
		break;
	
	case 'getFileIndex':
		include 'act_get_fileindex.php';
		echo json_encode($returnvalue);
		break;
	
	case 'setFileIndex':
		include 'act_set_fileindex.php';
		echo json_encode($returnvalue);
		break;
		
	case 'setFileIndexStart':
		include 'act_set_fileindex_start.php';
		echo json_encode($returnvalue);
		break;
		
	case 'setFileIndexMiddle':
		include 'act_set_fileindex_middle.php';
		echo json_encode($returnvalue);
		break;
		
	case 'setFileIndexEnd':
		include 'act_set_fileindex_end.php';
		echo json_encode($returnvalue);
		break;
		
		
	case 'downloadFile':
		include 'act_download_file.php';
		//echo json_encode($returnvalue);
		break;
	
	case 'uploadFile':
		
		// checksum
		//hash_file('md5', 'example.txt');
		
		include 'act_upload_file.php';
		break;
	
	
	default:
		echo json_encode(array('type' => 'error', 'message' => 'method not implemented', 'method' => $method));
}

?>