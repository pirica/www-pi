<?php
//$id_place = -1;
$error = 0;
$place_description = '';
$place_pre_description = '';
$place_lat_top = 'null';
$place_lon_right = 'null';
$place_lat_bottom = 'null';
$place_lon_left = 'null';

if(isset($_POST['description'])){
	$place_description = $_POST['description'];
}
if(isset($_POST['pre_description'])){
	$place_pre_description = $_POST['pre_description'];
}

if(isset($_POST['lat_top']) && $_POST['lat_top'] != '' && is_numeric($_POST['lat_top'])){
	$place_lat_top = $_POST['lat_top'];
}
if(isset($_POST['lon_right']) && $_POST['lon_right'] != '' && is_numeric($_POST['lon_right'])){
	$place_lon_right = $_POST['lon_right'];
}
if(isset($_POST['lat_bottom']) && $_POST['lat_bottom'] != '' && is_numeric($_POST['lat_bottom'])){
	$place_lat_bottom = $_POST['lat_bottom'];
}
if(isset($_POST['lon_left']) && $_POST['lon_left'] != '' && is_numeric($_POST['lon_left'])){
	$place_lon_left = $_POST['lon_left'];
}


if($id_place > 0){
	mysqli_query($conn, "
		update t_place
		set
			description = '" . mysqli_real_escape_string($conn, $place_description) . "',
			pre_description = '" . mysqli_real_escape_string($conn, $place_pre_description) . "',
			
			lat_top = " . $place_lat_top . ",
			lon_right = " . $place_lon_right . ",
			lat_bottom = " . $place_lat_bottom . ",
			lon_left = " . $place_lon_left . "
		where
			id_place = " . $id_place . "
		");
}
else {
	mysqli_query($conn, "
		insert into t_place
		(
			description,
			pre_description,
			
			lat_top,
			lon_right,
			lat_bottom,
			lon_left
		)
		values
		(
			'" . mysqli_real_escape_string($conn, $place_description) . "',
			'" . mysqli_real_escape_string($conn, $place_pre_description) . "',
			
			" . $place_lat_top . ",
			" . $place_lon_right . ",
			" . $place_lat_bottom . ",
			" . $place_lon_left . "
		)
		");
		
	$id_place = mysqli_insert_id($conn);
}
?>