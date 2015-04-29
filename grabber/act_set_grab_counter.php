<?php
$id_grab_counter = saneInput('id_grab_counter', 'int', -1);
$error = 0;

$counter_type = '';
$counter_field = '';

$counter_datefrom = '';
$counter_dateto = '';

$counter_intfrom = '';
$counter_intto = '';

$counter_listvalues = '';


if(isset($_POST['counter_type'])){
	$counter_type = $_POST['counter_type'];
}
if(isset($_POST['counter_field'])){
	$counter_field = $_POST['counter_field'];
}

if(isset($_POST['counter_datefrom'])){
	$counter_datefrom = $_POST['counter_datefrom'];
}
if(isset($_POST['counter_dateto'])){
	$counter_dateto = $_POST['counter_dateto'];
}

if(isset($_POST['counter_intfrom'])){
	$counter_intfrom = $_POST['counter_intfrom'];
}
if(isset($_POST['counter_intto'])){
	$counter_intto = $_POST['counter_intto'];
}

if(isset($_POST['counter_listvalues'])){
	$counter_listvalues = $_POST['counter_listvalues'];
}


if($id_grab_counter > 0){
	switch($counter_type){
		case 'date':
			mysql_query("
				update t_grab_counter
				set
					type = '" . mysql_real_escape_string($counter_type) . "',
					field = '" . mysql_real_escape_string($counter_field) . "',
					
					datefrom = '" . mysql_real_escape_string($counter_datefrom) . "',
					dateto = '" . mysql_real_escape_string($counter_dateto) . "',
					
					intfrom = null,
					intto = null,
					
					listvalues = null
					
				where
					id_grab_counter = " . $id_grab_counter . "
				", $conn);
			break;
			
		case 'int':
			mysql_query("
				update t_grab_counter
				set
					type = '" . mysql_real_escape_string($counter_type) . "',
					field = '" . mysql_real_escape_string($counter_field) . "',
					
					datefrom = null,
					dateto = null,
					
					intfrom = " . $counter_intfrom . ",
					intto = " . $counter_intto . ",
					
					listvalues = null
					
				where
					id_grab_counter = " . $id_grab_counter . "
				", $conn);
			break;
			
		case 'list':
			mysql_query("
				update t_grab_counter
				set
					type = '" . mysql_real_escape_string($counter_type) . "',
					field = '" . mysql_real_escape_string($counter_field) . "',
					
					datefrom = null,
					dateto = null,
					
					intfrom = null,
					intto = null,
					
					listvalues = '" . mysql_real_escape_string($counter_listvalues) . "'
					
				where
					id_grab_counter = " . $id_grab_counter . "
				", $conn);
			break;
	}
}
else {
	switch($counter_type){
		case 'date':
			mysql_query("
				insert into t_grab_counter
				(
					id_grab,
					type,
					field,
					
					datefrom,
					dateto,
					
					intfrom,
					intto,
					
					listvalues
					
				)
				values
				(
					" . $id_grab . ",
					'" . mysql_real_escape_string($counter_type) . "',
					'" . mysql_real_escape_string($counter_field) . "',
					
					'" . mysql_real_escape_string($counter_datefrom) . "',
					'" . mysql_real_escape_string($counter_dateto) . "',
					
					null,
					null,
					
					null
					
				)
				", $conn);
			break;
			
		case 'int':
			mysql_query("
				insert into t_grab_counter
				(
					id_grab,
					type,
					field,
					
					datefrom,
					dateto,
					
					intfrom,
					intto,
					
					listvalues
					
				)
				values
				(
					" . $id_grab . ",
					'" . mysql_real_escape_string($counter_type) . "',
					'" . mysql_real_escape_string($counter_field) . "',
					
					null,
					null,
					
					" . $counter_intfrom . ",
					" . $counter_intto . ",
					
					null
					
				)
				", $conn);
			break;
			
		case 'list':
			mysql_query("
				insert into t_grab_counter
				(
					id_grab,
					type,
					field,
					
					datefrom,
					dateto,
					
					intfrom,
					intto,
					
					listvalues
					
				)
				values
				(
					" . $id_grab . ",
					'" . mysql_real_escape_string($counter_type) . "',
					'" . mysql_real_escape_string($counter_field) . "',
					
					null,
					null,
					
					null,
					null,
					
					'" . mysql_real_escape_string($counter_listvalues) . "'
					
				)
				", $conn);
			break;
	}
	
}
?>