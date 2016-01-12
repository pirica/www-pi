<?php
require 'connection.php';
require 'functions.php';

require '../_core/webinit.php';

switch($action->getCode()){
	/*
	case 'login':
		require '../_core/dsp_header.php';
		require '../users/dsp_loginform.php';
		require '../_core/dsp_footer.php';
		break;
	*/
	
	case 'usage_now': // show per host usage graph for current hour, per minute
	case 'usage_today': // show per host usage graph for today, per hour
	case 'usage_day': // show per host usage graph for this month (this period)
	case 'usage_month': // show per host usage graph for previous periods
		require 'act_usage.php';
		
		require '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		require 'dsp_usage.php';
		require '../_core/dsp_footer.php';
		
		break;
	
	case 'status':
		// show hosts online graph
		// => kind of prosteps planning tool, but for when online
		include 'act_status.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_status.php';
		include '../_core/dsp_footer.php';
		
		break;
	
	
	case 'config':
		$show_all = saneInput('all', 'int', $settings->val('config_showall_default_value', 0));
		include 'queries/pr_get_hosts.php';
		include 'queries/pr_get_categories.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_config.php';
		include '../_core/dsp_footer.php';
        break;
		
	case 'do_sethost':
		include 'act_set_host.php';
		//goto_action('main', false, 'id_host=' . $id_host);
		break;
		
	case 'jmain':
		include 'queries/pr_get_hosts.php';
		include 'js_main.php';
		break;
		
	// main: overview
	default:
		include 'queries/pr_get_hosts.php';
		
		include '../_core/dsp_header.php';
		require 'dsp_submenu.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
        break;
		
}
?>