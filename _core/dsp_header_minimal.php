<!DOCTYPE html>
<html>
<head>
	<?php
		/*if(true){
			echo '<!--' . "\n";
			echo 'id:' . $app->getId() . "\n";
			echo 'id:' . $action->getId() . '-' . $action->getCode() . "\n";
			echo '=====' . "\n";
			echo 'a.lir:' . $action->getLoginRequired() . "\n";
			echo 'li:' . $loggedin . "\n";
			echo 'a.a:' . $action->getAllowed() . "\n";
			echo '=====' . "\n";
			//echo $app->getBaseUrl();
			//$settings->_debug();
			echo '=====' . "\n";
			print_r($_SESSION);
			echo '-->';
		}
		// */
	?>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="<?= $app->getInfo() ?>" name="description">
	<meta content="Wikke" name="author">
	<title><?= $app->getName() . ($app->getTitle() != '' ? ' - ' . $app->getTitle() : '') ?></title>
	
	<link rel="stylesheet" href="../_assets/styles/bootstrap-3.1.0.min.css">
	<link rel="stylesheet" href="../_assets/styles/bootstrap_custom.css">
	<link rel="stylesheet" type="text/css" href="styles/default.css"/>

	<script src="../_assets/scripts/jquery/jquery-1.10.2.min.js"></script>
	<script src="../_assets/scripts/bootstrap/bootstrap-3.1.0.min.js"></script>
	
	<script src="../_assets/scripts/jquery/jquery.validate.js"></script>
	<script src="../_assets/scripts/jquery/additional-methods.js"></script>
	
	<script type="text/javascript" src="../_assets/scripts/bootstrap/daterangepicker/moment.js"></script>
	<script type="text/javascript" src="../_assets/scripts/bootstrap/daterangepicker/daterangepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="../_assets/styles/bootstrap/daterangepicker-bs3.css" />
	
	<link href="../_assets/styles/font-awesome.min.css" rel="stylesheet">

	<script type="text/javascript" src="scripts/functions.js"></script>
	
	<?php
		
		echo $app->getHeaderScripts();
		
		//echo '<script type="text/javascript">var id_grab = ' . $id_grab . ';</script>';
		
		switch($action->getCode()){
			case 'login':
				echo '<link rel="stylesheet" href="../users/styles/default.css" />';
				echo '<script type="text/javascript" src="../users/scripts/sha512.js"></script>';
				echo '<script type="text/javascript" src="../users/scripts/forms.js"></script>';
				break;
				
			case 'register':
				echo '<link rel="stylesheet" href="../users/styles/default.css" />';
				echo '<script type="text/javascript" src="../users/scripts/sha512.js"></script>';
				echo '<script type="text/javascript" src="../users/scripts/forms.js"></script>';
				break;
				
			/*
			case 'details':
				echo '<script type="text/javascript">var sort = "' . $sort . '", sortorder = "' . $sortorder . '", perpage = ' . $perpage . ', page = ' . $page . ', status = "' . $status . '", search = "' . $search . '";</script>';
				break;
			*/
		}
		
		if($app->videojs === true)
		{
			echo '<link rel="stylesheet" href="../_assets/styles/videojs.css" />';
			echo '<script type="text/javascript" src="../_assets/scripts/videojs.js"></script>';
		}
		
	?>
</head>
<body>
	
<div id="wrap">
	
	<?php
	include '../users/dsp_topmenu_minimal.php';
	?>
	
	<div class="container">
	