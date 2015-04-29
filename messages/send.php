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


/**
 *	send a NotifyMyAndroid message
 *	notify($application = '', $event = '', $description = '', $priority = 0, $apiKeys = false, $options = array())
 */
//$nma = new nmaApi(array('apikey' => 'ae87ee05b547fc07cc129eeef0406b061a32ecdf189197e6'));
//$nma = new nmaApi(array('apikey' => 'f5a6f0ddebd08006dea7f2398a79f7de1499a4a33844c468'));







if(isset($_GET['msg']) && $_GET['msg'] != ''){
	$msg = $_GET['msg'];
	
	if(isset($_GET['channel']) && $_GET['channel'] != ''){
		$channel = $_GET['channel'];
	}
}
else if(isset($argv) && count($argv) > 1 && $argv[1] != '' && $argv[2] != ''){
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


if(isset($_GET['host']) && $_GET['host'] != ''){
	$host = $_GET['host'];
}
if(isset($_GET['priority']) && ($_GET['priority'] == -2 || $_GET['priority'] == -1 || $_GET['priority'] == 0 || $_GET['priority'] == 1 || $_GET['priority'] == 2)){
	$priority = $_GET['priority'];
}


send_msg($channel, $title, $msg, $priority, ($by_shell ? 'shell' : 'url'));

/*
$response = [];

$result_nma = 0;
$result_ortc = 0;

if($channel != '' && $msg != ''){
	
	$result_nma = send_nma_msg($channel, $title, $msg, $priority) ? 2 : 0;
	if(!$by_shell){
		echo 'NMA response:'. $result_nma . " <br>\n";
		//print_r($response);
	}
	
	$result_ortc = send_ortc_msg($channel, $msg, $response) ? 1 : 0;
	if(!$by_shell){
		echo 'ORTC response:'. $result_ortc . " <br>\n";
		print_r($response);
	}
	
}
else {
	echo 'message or channel not set';
}

log_message('ORTC,NMA,' . ($by_shell ? 'shell' : 'url'), $host, $channel, $title, $msg, $result_nma + $result_ortc);
*/
?>