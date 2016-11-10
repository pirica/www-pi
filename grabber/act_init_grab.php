<?php
$error = 0;
$grab_description = '';
$grab_url = '';
$grab_path = '';
$grab_filename = '';

$grab_max_grabbers = -1;
$grab_excluded = '';
$grab_excluded_size = -1;
$grab_always_retry = 0;
$grab_script_completion = '';
$grab_remove_completed_after_days = -1;
$grab_remove_inactive_after_months = -1;
$grab_keep_diskspace_free = -1;
$grab_scheduled = 0;

$grab_counters_total = 0;
$grab_files_total = 0;
$grab_files_done = 0;
$grab_files_exist = 0;
$grab_files_todo = 0;

$grab_eta = '';
$grab_eta_rounded = '';
		
while($grab = mysqli_fetch_array($qry_grabs)){
	if($grab['id_grab'] == $id_grab){
		$grab_description = $grab['description'];
		$grab_url = $grab['url'];
		$grab_path = $grab['path'];
		$grab_filename = $grab['filename'];
		
		$grab_max_grabbers = $grab['max_grabbers'];
		$grab_excluded = $grab['excluded'];
		$grab_excluded_size = $grab['excluded_size'];
		$grab_always_retry = $grab['always_retry'];
		$grab_script_completion = $grab['script_completion'];
		$grab_remove_completed_after_days = $grab['remove_completed_after_days'];
		$grab_remove_inactive_after_months = $grab['remove_inactive_after_months'];
		$grab_keep_diskspace_free = $grab['keep_diskspace_free'];
		$grab_scheduled = $grab['scheduled'];
		
		$grab_counters_total = $grab['counters_total'];
		$grab_files_total = $grab['files_total'];
		$grab_files_done = $grab['files_done'];
		$grab_files_exist = $grab['files_exist'];
		$grab_files_todo = $grab['files_todo'];
		
		$grab_eta = $grab['eta'];
		$grab_eta_rounded = $grab['eta_rounded'];
	}
}

?>