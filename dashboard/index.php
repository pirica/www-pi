<?php
include 'connection.php';
//include 'functions.php';
require 'functions.php';

require '../_core/appinit.php';


switch($action->getCode()){
	
	case 'login':
		include '../_core/dsp_header.php';
		include '../users/dsp_loginform.php';
		include '../_core/dsp_footer.php';
		break;
	
	
	case 'weather':
		$app->();
		break;
		
	// main: overview
	default:
		//include 'act_main.php';
		
		include '../_core/dsp_header.php';
		//include 'dsp_submenu.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
	
}
?>