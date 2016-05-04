<?php
//require 'connection.php';
//require 'functions.php';

require '../_core/webinit.php';

switch($action->getCode()){

	case 'login':
		require '../_core/dsp_header.php';
		require 'dsp_loginform.php';
		require '../_core/dsp_footer.php';
		break;
		
	case 'do_login':
		require 'act_login.php';
		break;
		
	case 'loggedin':
		require '../_core/dsp_header.php';
		require 'dsp_loggedin.php';
		require '../_core/dsp_footer.php';
		break;
	
		
	case 'logout':
		include_once 'act_logout.php';
		
		//require '../_core/dsp_header.php';
		//require 'dsp_register.php';
		//require '../_core/dsp_footer.php';
		break;
	
	
	case 'register':
		include_once 'act_register.php';
		
		require '../_core/dsp_header.php';
		//require 'dsp_register.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'register_success':
		require '../_core/dsp_header.php';
		require 'dsp_register_success.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	case 'error':
		require '../_core/dsp_header.php';
		require 'dsp_error.php';
		require '../_core/dsp_footer.php';
		break;
	
	
	// management
	
	case 'settings':
		require '../_core/dsp_header.php';
		
		require 'queries/pr_get_profiles.php';
		require 'queries/pr_get_settings.php';
		require 'dsp_settings.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'do_setsetting':
		include 'act_set_setting.php';
		break;
		
		
	case 'actions':
		require 'queries/pr_get_actions.php';
		require '../_core/dsp_header.php';
		require 'dsp_actions.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'do_setaction':
		include 'act_set_action.php';
		break;
			
	
	case 'profileapps':
		require 'queries/pr_get_profile_apps.php';
		require '../_core/dsp_header.php';
		require 'dsp_profile_apps.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'do_setprofileapp':
		include 'act_set_profileapp.php';
		break;
		
		
	case 'profileappactions':
		require 'queries/pr_get_profile_app_actions.php';
		require '../_core/dsp_header.php';
		require 'dsp_profile_app_actions.php';
		require '../_core/dsp_footer.php';
		break;
	
	case 'do_setprofileappaction':
		include 'act_set_profileappaction.php';
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