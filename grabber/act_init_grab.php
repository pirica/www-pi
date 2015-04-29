<?php
$error = 0;
$grab_description = '';
$grab_url = '';
$grab_path = '';
$grab_filename = '';
$grab_excluded = '';
$grab_max_grabbers = 1;

$grab_counters_total = 0;
$grab_files_total = 0;
$grab_files_done = 0;
$grab_files_exist = 0;
$grab_files_todo = 0;

$grab_eta = '';
$grab_eta_rounded = '';
		
while($grab = mysql_fetch_array($qry_grabs)){
	if($grab['id_grab'] == $id_grab){
		$grab_description = $grab['description'];
		$grab_url = $grab['url'];
		$grab_path = $grab['path'];
		$grab_filename = $grab['filename'];
		$grab_excluded = $grab['excluded'];
		$grab_max_grabbers = $grab['max_grabbers'];
		
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