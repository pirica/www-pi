<?php
include 'connection.php';
include 'act_settings.php';
include 'functions.php';


$check_fileindex = exec('ps aux | grep "filerep/build_fileindex.php" | grep -v grep');
$check_directoryindex = exec('ps aux | grep "filerep/build_directory_index.php" | grep -v grep');
$check_directoryindex .= exec('ps aux | grep "filerep/check_shares.php" | grep -v grep');
$check_shareindex = exec('ps aux | grep "filerep/build_shares.php" | grep -v grep');

$check_fileindex = str_replace(" ", "", $check_fileindex);
$check_fileindex = str_replace("\r", "", $check_fileindex);
$check_fileindex = str_replace("\n", "", $check_fileindex);
$check_fileindex = str_replace("\t", "", $check_fileindex);

$check_directoryindex = str_replace(" ", "", $check_directoryindex);
$check_directoryindex = str_replace("\r", "", $check_directoryindex);
$check_directoryindex = str_replace("\n", "", $check_directoryindex);
$check_directoryindex = str_replace("\t", "", $check_directoryindex);

$check_shareindex = str_replace(" ", "", $check_shareindex);
$check_shareindex = str_replace("\r", "", $check_shareindex);
$check_shareindex = str_replace("\n", "", $check_shareindex);
$check_shareindex = str_replace("\t", "", $check_shareindex);

if($setting_fileindex_running != '0' && $check_fileindex == ''){
	mysqli_query($conn, "update t_setting set value = '0' where code = 'fileindex_running'");
	echo "[" . date('Y-m-d H:i:s', time()) . "] " . "filerep -> fileindex cleared" . "'\n";
}

if($setting_directoryindex_running != '0' && $check_directoryindex == ''){
	mysqli_query($conn, "update t_setting set value = '0' where code = 'directoryindex_running'");
	echo "[" . date('Y-m-d H:i:s', time()) . "] " . "filerep -> directoryindex cleared" . "'\n";
}

if($setting_shareindex_running != '0' && $check_shareindex == ''){
	mysqli_query($conn, "update t_setting set value = '0' where code = 'shareindex_running'");
	echo "[" . date('Y-m-d H:i:s', time()) . "] " . "filerep -> shareindex cleared" . "'\n";
}


?>