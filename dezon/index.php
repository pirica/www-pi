<?php
require '../_core/webinit.php';

include 'connections.php';
include 'functions.php';

switch($action->getCode()){
	
	// main: overview
	default:
		$app->setTitle('Thuisverpleging De Zon');
		
		require '../_core/dsp_header.php';
		include 'dsp_main.php';
		require '../_core/dsp_footer.php';
	
	
}
?>