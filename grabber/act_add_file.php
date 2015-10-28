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

// get youtube
if(strpos($grab_url, 'youtube.com') !== false && strpos($grab_filename, '.mp3') !== false){
	$yt = json_decode(file_get_contents('http://youtubeinmp3.com/fetch/?format=JSON&video=' . $grab_url));
	
	$grab_url = $yt->link;
}

if($id_grab > 0 && $grab_url != '' && $grab_full_path != ''){
	mysql_query("
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
			'" . mysql_real_escape_string($grab_url) . "',
			'" . mysql_real_escape_string($grab_referer) . "',
			'" . mysql_real_escape_string($grab_full_path) . "',
			'" . mysql_real_escape_string($grab_type) . "'
		)
		", $conn);
		
		$id_grab_file = mysql_insert_id($conn);
		
	if($grab_filename == ''){
		
		if($grab_type == 'youtube-dl'){
			$grab_full_path = $grab_path . '%(title)s.%(ext)s';
		}
		else {
			$grab_full_path = $grab_path . 'download_' . $id_grab_file . '.tmp';
		}
		
		mysql_query("
			update t_grab_file
			set
				full_path = '" . mysql_real_escape_string($grab_full_path) . "'
			where
				id_grab_file = " . $id_grab_file . "
			", $conn);
	}
}
?>