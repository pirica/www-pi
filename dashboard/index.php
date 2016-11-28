<?php
require '../_core/webinit.php';

include 'connection.php';
require 'functions.php';


switch($action->getCode()){
	
	case 'weather':
		$app->jqueryui = true;
		
		$app->setHeaderScripts('<link href="http://fonts.googleapis.com/css?family=Nixie+One|Yanone+Kaffeesatz:400,200,700,300" rel="stylesheet" type="text/css">');
		$app->setHeaderScripts('<script type="text/javascript" src="scripts/skycons.js"></script>');
		$app->setHeaderScripts('<script type="text/javascript" src="http://suncalc.net/scripts/suncalc.js"></script>');
		$app->setHeaderScripts('<link href="styles/weather.css" rel="stylesheet" type="text/css">');
		$app->setHeaderScripts('<script src="http://momentjs.com/downloads/moment.min.js"></script>');
		
		include '../_core/dsp_header.php';
		include 'dsp_weather.php';
		include '../_core/dsp_footer.php';
		break;
	
	case 'traffic':
		
		include '../_core/dsp_header.php';
		include 'dsp_traffic.php';
		include '../_core/dsp_footer.php';
		break;
	
	// main: overview
	default:
		include '../_core/dsp_header.php';
		include 'dsp_main.php';
		include '../_core/dsp_footer.php';
	
	
}
?>