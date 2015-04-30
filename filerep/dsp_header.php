<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<!--meta content="width=device-width, initial-scale=1.0" name="viewport"-->
	<meta content="Filerep web interface" name="description">
	<meta content="Wikke" name="author">
	<title>File replication<?=($pagetitle != '' ? ' - ' . $pagetitle : '')?></title>

	<link rel="stylesheet" href="../_assets/styles/bootstrap-3.1.0.min.css">
	<link rel="stylesheet" href="../_assets/styles/bootstrap_custom.css">
	<link rel="stylesheet" type="text/css" href="styles/default.css"/>

	<script src="../_assets/scripts/jquery/jquery-1.10.2.min.js"></script>
	<script src="../_assets/scripts/bootstrap/bootstrap-3.1.0.min.js"></script>
	
	<script src="../_assets/scripts/jquery/jquery.validate.js"></script>
	<script src="../_assets/scripts/jquery/additional-methods.js"></script>
	
	<!--
	<script type="text/javascript" src="../_assets/scripts/bootstrap/daterangepicker/moment.js"></script>
	<script type="text/javascript" src="../_assets/scripts/bootstrap/daterangepicker/daterangepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="../_assets/styles/bootstrap/daterangepicker-bs3.css" />
	-->
	
	<link href="../_assets/styles/font-awesome.min.css" rel="stylesheet">

	<script type="text/javascript" src="scripts/functions.js"></script>

	<?php
		


		echo '<script type="text/javascript">var id_share = ' . $id_share . ', id_host = ' . $id_host . ', id_file = ' . $id_file . ';</script>';
		
		switch($action->getCode()){
			case 'login':
				echo '<link rel="stylesheet" href="../users/styles/main.css" />';
				echo '<script type="text/javascript" src="../users/scripts/sha512.js"></script>';
				echo '<script type="text/javascript" src="../users/scripts/forms.js"></script>';
				break;
			
			/*case 'details':


				echo '<script type="text/javascript">var sort = "' . $sort . '", sortorder = "' . $sortorder . '", perpage = ' . $perpage . ', page = ' . $page . ', status = "' . $status . '", search = "' . $search . '";</script>';
				break;
			*/
			
		}
		
	?>
</head>
<body>
	
	<?php
	include '../users/dsp_topmenu.php';
	?>
	
	<ol class="breadcrumb">
		<li><a href="?action=main">&lt;All shares&gt;</a></li>
		
		<?php
			
			switch($action->getCode()){
				case 'details':
					echo '<li class="active">Edit grab</li>';
					break;
				




			}
			
		?>
	</ol>
	
	<div class="container">
	