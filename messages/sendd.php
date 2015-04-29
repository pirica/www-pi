<?php
error_reporting(E_ALL);

require dirname(__FILE__).'/functions.php';

//parse_str(implode('&', array_slice($argv, 1)), $_GET);

$host = '';
$channel = '';
$title = '';
$msg = '';
$priority = 0;

$by_shell = false;


if(isset($_GET['msg']) && $_GET['msg'] != ''){
	$msg = $_GET['msg'];
	
	if(isset($_GET['channel']) && $_GET['channel'] != ''){
		$channel = $_GET['channel'];
	}
}
else if(count($argv) > 1 && $argv[1] != '' && $argv[2] != ''){
	$channel = $argv[1];
	
	if(count($argv) > 2 && ($argv[2] == -2 || $argv[2] == -1 || $argv[2] == 0 || $argv[2] == 1 || $argv[2] == 2)){
		$priority = $argv[2];
		$msg = join(' ', array_slice($argv, 3));
	}
	else {
		$msg = join(' ', array_slice($argv, 2));
	}
	
	$by_shell = true;
}


/* check if sent this hour */
//$date_check = date("Y-m-d H:00:00");

/* check if sent today */
$date_check = date("Y-m-d 00:00:00");

/* check if sent this week - week starts on monday */
//$week_start = (date('w') == 0 ? time() : strtotime('last sunday')) + (60 * 60 * 24);
//$date_check = date("Y-m-d 00:00:00", $week_start);

/* check if sent this month */
//$date_check = date("Y-m-01 00:00:00");


if(!check_msg_already_sent($channel, $title, $msg, $date_check)){

	send_msg($channel, $title, $msg, $priority, ($by_shell ? 'shell' : 'url'));
	
}

?>