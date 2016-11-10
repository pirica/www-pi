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
			mysqli_query($conn, "
				update t_grab_counter
				set
					type = '" . mysqli_real_escape_string($conn, $counter_type) . "',
					field = '" . mysqli_real_escape_string($conn, $counter_field) . "',
					
					datefrom = '" . mysqli_real_escape_string($conn, $counter_datefrom) . "',
					dateto = '" . mysqli_real_escape_string($conn, $counter_dateto) . "',
					
					intfrom = null,
					intto = null,
					
					listvalues = null
					
				where
					id_grab_counter = " . $id_grab_counter . "
				");
			break;
			
		case 'int':
			mysqli_query($conn, "
				update t_grab_counter
				set
					type = '" . mysqli_real_escape_string($conn, $counter_type) . "',
					field = '" . mysqli_real_escape_string($conn, $counter_field) . "',
					
					datefrom = null,
					dateto = null,
					
					intfrom = " . $counter_intfrom . ",
					intto = " . $counter_intto . ",
					
					listvalues = null
					
				where
					id_grab_counter = " . $id_grab_counter . "
				");
			break;
			
		case 'list':
			mysqli_query($conn, "
				update t_grab_counter
				set
					type = '" . mysqli_real_escape_string($conn, $counter_type) . "',
					field = '" . mysqli_real_escape_string($conn, $counter_field) . "',
					
					datefrom = null,
					dateto = null,
					
					intfrom = null,
					intto = null,
					
					listvalues = '" . mysqli_real_escape_string($conn, $counter_listvalues) . "'
					
				where
					id_grab_counter = " . $id_grab_counter . "
				");
			break;
	}
}
else {
	switch($counter_type){
		case 'date':
			mysqli_query($conn, "
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
					'" . mysqli_real_escape_string($conn, $counter_type) . "',
					'" . mysqli_real_escape_string($conn, $counter_field) . "',
					
					'" . mysqli_real_escape_string($conn, $counter_datefrom) . "',
					'" . mysqli_real_escape_string($conn, $counter_dateto) . "',
					
					null,
					null,
					
					null
					
				)
				");
			break;
			
		case 'int':
			mysqli_query($conn, "
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
					'" . mysqli_real_escape_string($conn, $counter_type) . "',
					'" . mysqli_real_escape_string($conn, $counter_field) . "',
					
					null,
					null,
					
					" . $counter_intfrom . ",
					" . $counter_intto . ",
					
					null
					
				)
				");
			break;
			
		case 'list':
			mysqli_query($conn, "
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
					'" . mysqli_real_escape_string($conn, $counter_type) . "',
					'" . mysqli_real_escape_string($conn, $counter_field) . "',
					
					null,
					null,
					
					null,
					null,
					
					'" . mysqli_real_escape_string($conn, $counter_listvalues) . "'
					
				)
				");
			break;
	}
	
}
?>