<?php
//$id_grab = -1;
$error = 0;

$grab_type = '';
$grab_url = '';
$grab_referer = '';
$grab_full_path = '';
/*
$grab_path = '';
$grab_filename = '';
*/

if(isset($_POST['grab_type'])){
	$grab_type = $_POST['grab_type'];
}
if(isset($_POST['grab_url'])){
	$grab_url = $_POST['grab_url'];
}
if(isset($_POST['grab_referer'])){
	$grab_referer = $_POST['grab_referer'];
}
if(isset($_POST['grab_path']) && $_POST['grab_path'] != ''){
	$grab_path = $_POST['grab_path'];
}
if(isset($_POST['grab_filename']) && $_POST['grab_filename'] != ''){
	$grab_filename = $_POST['grab_filename'];
}
else {
	$fileparts = explode('/', $grab_url);
	$grab_filename = $fileparts[count($fileparts) - 1];
	
	$grab_filename = str_replace('\r', '', $grab_filename);
	$grab_filename = str_replace('\n', '', $grab_filename);
	//$grab_filename = str_replace(' ', '', $grab_filename);
	$grab_filename = str_replace('/', ' ', $grab_filename);
	$grab_filename = str_replace(':', ' ', $grab_filename);
	$grab_filename = str_replace('*', '', $grab_filename);
	$grab_filename = str_replace('?', '', $grab_filename);
	$grab_filename = str_replace('"', '', $grab_filename);
	$grab_filename = str_replace('<', '', $grab_filename);
	$grab_filename = str_replace('>', '', $grab_filename);
	$grab_filename = str_replace('|', '', $grab_filename);
	$grab_filename = str_replace('[', '', $grab_filename);
	$grab_filename = str_replace(']', '', $grab_filename);
	$grab_filename = str_replace('(', '', $grab_filename);
	$grab_filename = str_replace(')', '', $grab_filename);
	$grab_filename = str_replace('^', '', $grab_filename);
	$grab_filename = str_replace('!', '', $grab_filename);
	$grab_filename = str_replace(':', '', $grab_filename);
	$grab_filename = str_replace('=', '', $grab_filename);
	$grab_filename = str_replace('{', '', $grab_filename);
	$grab_filename = str_replace('}', '', $grab_filename);
	$grab_filename = str_replace('\'', '', $grab_filename);
	$grab_filename = str_replace('~', '', $grab_filename);
	
	$grab_filename = str_replace('\t', ' ', $grab_filename);
	$grab_filename = str_replace('+', ' ', $grab_filename);
	
	$grab_filename = str_replace('#', '-hash-', $grab_filename);
	$grab_filename = str_replace('%', '-pct-', $grab_filename);
	$grab_filename = str_replace('&', '-and-', $grab_filename);
	$grab_filename = str_replace('@', '-at-', $grab_filename);
	
	$grab_filename = str_replace('  ', ' ', $grab_filename);
	$grab_filename = str_replace('  ', ' ', $grab_filename);
}

if(substr($grab_path, -1, 1) != '/'){
	$grab_path = $grab_path . '/';
}

$grab_full_path = $grab_path . $grab_filename;


if($id_grab > 0 && $grab_url != '' && $grab_full_path != ''){
	mysqli_query($conn, "
		insert into t_grab_file
		(
			id_grab,
			full_url,
			referer,
			full_path,
			type
		)
		values
		(
			" . $id_grab . ",
			'" . mysqli_real_escape_string($conn, $grab_url) . "',
			'" . mysqli_real_escape_string($conn, $grab_referer) . "',
			'" . mysqli_real_escape_string($conn, $grab_full_path) . "',
			'" . mysqli_real_escape_string($conn, $grab_type) . "'
		)
		");
		
		$id_grab_file = mysqli_insert_id($conn);
		
	if($grab_filename == ''){
		
		$grab_full_path = $grab_path . 'download_' . $id_grab_file . '.tmp';
		
		mysqli_query($conn, "
			update t_grab_file
			set
				full_path = '" . mysqli_real_escape_string($conn, $grab_full_path) . "'
			where
				id_grab_file = " . $id_grab_file . "
			");
	}
}
?>