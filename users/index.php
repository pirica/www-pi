<?php
require 'connection.php';
require 'functions.php';

require '../_core/appinit.php';

switch($action->getCode()){

	case 'login':
		require '../_core/dsp_header.php';
		require '../users/dsp_loginform.php';
		require '../_core/dsp_footer.php';
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