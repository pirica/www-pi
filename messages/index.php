<?php

//require 'connection.php';
require 'functions.php';

require '../_core/appinit.php';


switch($action->getCode()){

	case 'login':
		require '../_core/dsp_header.php';
		require '../users/dsp_loginform.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	case 'messages':
		include 'queries/pr_get_log_messages.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_messages.php';
		include '../_core/dsp_footer.php';
        break;
		
	case 'alerts_email':
		include 'queries/pr_get_alerts_email.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_manage_alerts_email.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'alerts_tt':
		//include 'queries/pr_get_alert_tracking_types.php';
		include 'queries/pr_get_alerts_tt.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_manage_alerts_tt.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'php_errors':
		include 'act_php_errors.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_php_errors.php';
		include '../_core/dsp_footer.php';
		break;
	
	
	case 'manage_alerts_email':
		include 'queries/pr_get_alerts_email.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_manage_alerts_email.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'manage_alerts_tt':
		//include 'queries/pr_get_alert_tracking_types.php';
		include 'queries/pr_get_alerts_tt.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_manage_alerts_tt.php';
		include '../_core/dsp_footer.php';
		break;
	
	
	// main: overview
	default:
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
        break;
		
}
?>