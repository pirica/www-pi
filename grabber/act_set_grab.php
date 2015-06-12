<?php
//$id_grab = -1;
$error = 0;
$grab_description = '';
$grab_url = '';
$grab_path = '';
$grab_filename = '';

$grab_max_grabbers = 'null';
$grab_excluded = '';
$grab_excluded_size = -1;
$grab_always_retry = 0;
$grab_script_completion = '';
$grab_remove_completed_after_days = -1;
$grab_remove_inactive_after_months = -1;
$grab_keep_diskspace_free = 0;
$grab_scheduled = 0;

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

if(isset($_POST['grab_max_grabbers']) && $_POST['grab_max_grabbers'] != '' && is_numeric($_POST['grab_max_grabbers']) && $_POST['grab_max_grabbers'] > 0){
	$grab_max_grabbers = $_POST['grab_max_grabbers'];
}
if(isset($_POST['grab_excluded'])){
	$grab_excluded = $_POST['grab_excluded'];
}
if(isset($_POST['grab_excluded_size']) && $_POST['grab_excluded_size'] != '' && is_numeric($_POST['grab_excluded_size']) && $_POST['grab_excluded_size'] > 0){
	$grab_excluded_size = $_POST['grab_excluded_size'];
}
if(isset($_POST['grab_always_retry']) && ($_POST['grab_always_retry'] == 0 || $_POST['grab_always_retry'] == 1)){
	$grab_always_retry = $_POST['grab_always_retry'];
}
if(isset($_POST['grab_script_completion'])){
	$grab_script_completion = $_POST['grab_script_completion'];
}
if(isset($_POST['grab_remove_completed_after_days']) && $_POST['grab_remove_completed_after_days'] != '' && is_numeric($_POST['grab_remove_completed_after_days']) && $_POST['grab_remove_completed_after_days'] > 0){
	$grab_remove_completed_after_days = $_POST['grab_remove_completed_after_days'];
}
if(isset($_POST['grab_remove_inactive_after_months']) && $_POST['grab_remove_inactive_after_months'] != '' && is_numeric($_POST['grab_remove_inactive_after_months']) && $_POST['grab_remove_inactive_after_months'] > 0){
	$grab_remove_inactive_after_months = $_POST['grab_remove_inactive_after_months'];
}
if(isset($_POST['grab_keep_diskspace_free']) && $_POST['grab_keep_diskspace_free'] != '' && is_numeric($_POST['grab_keep_diskspace_free']) && $_POST['grab_keep_diskspace_free'] > 0){
	$grab_keep_diskspace_free = $_POST['grab_keep_diskspace_free'];
}
if(isset($_POST['grab_scheduled']) && ($_POST['grab_scheduled'] == 0 || $_POST['grab_scheduled'] == 1)){
	$grab_scheduled = $_POST['grab_scheduled'];
}

if(substr($grab_path, -1, 1) != '/'){
	$grab_path = $grab_path . '/';
}


if($id_grab > 0){
	mysql_query("
		update t_grab
		set
			description = '" . mysql_real_escape_string($grab_description) . "',
			url = '" . mysql_real_escape_string($grab_url) . "',
			path = '" . mysql_real_escape_string($grab_path) . "',
			filename = '" . mysql_real_escape_string($grab_filename) . "',
			
			max_grabbers = " . $grab_max_grabbers . ",
			excluded = '" . mysql_real_escape_string($grab_excluded) . "',
			excluded_size = " . $grab_excluded_size . ",
			always_retry = " . $grab_always_retry . ",
			script_completion = '" . mysql_real_escape_string($grab_script_completion) . "',
			remove_completed_after_days = " . $grab_remove_completed_after_days . ",
			remove_inactive_after_months = " . $grab_remove_inactive_after_months . ",
			keep_diskspace_free = " . $grab_keep_diskspace_free . ",
			scheduled = " . $grab_scheduled . "
			
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
			
			max_grabbers,
			excluded,
			excluded_size,
			always_retry,
			script_completion,
			remove_completed_after_days,
			remove_inactive_after_months,
			keep_diskspace_free,
			scheduled
		)
		values
		(
			'" . mysql_real_escape_string($grab_description) . "',
			'" . mysql_real_escape_string($grab_url) . "',
			'" . mysql_real_escape_string($grab_path) . "',
			'" . mysql_real_escape_string($grab_filename) . "',
			
			" . $grab_max_grabbers . ",
			'" . mysql_real_escape_string($grab_excluded) . "',
			" . $grab_excluded_size . ",
			" . $grab_always_retry . ",
			'" . mysql_real_escape_string($grab_script_completion) . "',
			" . $grab_remove_completed_after_days . ",
			" . $grab_remove_inactive_after_months . ",
			" . $grab_keep_diskspace_free . ",
			" . $grab_scheduled . "
		)
		", $conn);
}
?>