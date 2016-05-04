<?php
require '../_core/webinit.php';

include 'connection.php';
require 'functions.php';


switch($action->getCode()){
	
	case 'weather':
		//$app->();
		include 'dsp_weather.php';
		break;
		
	// main: overview
	default:
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
	
}
?>