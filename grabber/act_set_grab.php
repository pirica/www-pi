<?php
//$id_grab = -1;
$error = 0;
$grab_description = '';
$grab_url = '';
$grab_path = '';
$grab_filename = '';
$grab_excluded = '';
$grab_max_grabbers = 1;

if(isset($_POST['grab_description'])){
	$grab_description = $_POST['grab_description'];
}
if(isset($_POST['grab_url'])){
	$grab_url = $_POST['grab_url'];
}
if(isset($_POST['grab_path'])){
	$grab_path = $_POST['grab_path'];
}
if(isset($_POST['grab_filename'])){
	$grab_filename = $_POST['grab_filename'];
}
if(isset($_POST['grab_excluded'])){
	$grab_excluded = $_POST['grab_excluded'];
}
if(isset($_POST['grab_max_grabbers']) && $_POST['grab_max_grabbers'] != '' && is_numeric($_POST['grab_max_grabbers']) && $_POST['grab_max_grabbers'] > 0){
	$grab_max_grabbers = $_POST['grab_max_grabbers'];
}


if($id_grab > 0){
	mysql_query("
		update t_grab
		set
			description = '" . mysql_real_escape_string($grab_description) . "',
			url = '" . mysql_real_escape_string($grab_url) . "',
			path = '" . mysql_real_escape_string($grab_path) . "',
			filename = '" . mysql_real_escape_string($grab_filename) . "',
			excluded = '" . mysql_real_escape_string($grab_excluded) . "',
			max_grabbers = " . $grab_max_grabbers . "
		where
			id_grab = " . $id_grab . "
		", $conn);
}
else {
	mysql_query("
		insert into t_grab
		(
			description,
			url,
			path,
			filename,
			excluded,
			max_grabbers
		)
		values
		(
			'" . mysql_real_escape_string($grab_description) . "',
			'" . mysql_real_escape_string($grab_url) . "',
			'" . mysql_real_escape_string($grab_path) . "',
			'" . mysql_real_escape_string($grab_filename) . "',
			'" . mysql_real_escape_string($grab_excluded) . "',
			" . $grab_max_grabbers . "
		)
		", $conn);
}
?>