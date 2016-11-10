<?php
require dirname(__FILE__).'/connection.php';
//session_start();
require dirname(__FILE__).'/ortc_calls.php';
//require dirname(__FILE__).'/nmaApi.class.php';
require dirname(__FILE__).'/nma_calls.php';

function my_escape_string($val){
	$val = str_replace("'", "\'", $val);
	$val = str_replace(";", "\;", $val);
	return $val;
}

function log_message($type, $host, $channel, $title, $message, $success){
	$qry = "insert into t_log_message (";
	$qry .= "type, ";
	$qry .= "host, ";
	$qry .= "channel, ";
	$qry .= "title, ";
	$qry .= "message, ";
	$qry .= "success, ";
	$qry .= "date_sent ";
	$qry .= ") ";
	$qry .= "values ";
	$qry .= "( ";
	$qry .= "'" . my_escape_string($type) . "', ";
	$qry .= "'" . my_escape_string($host) . "', ";
	$qry .= "'" . my_escape_string($channel) . "', ";
	$qry .= "'" . my_escape_string($title) . "', ";
	$qry .= "'" . my_escape_string($message) . "', ";
	$qry .= "'" . my_escape_string($success) . "', ";
	$qry .= "'" . date('Y-m-d H:i:s', time()) . "' ";
	$qry .= "); ";
	$qry .= "\r\n";
	
	if(!mysqli_query($GLOBALS['conn'], $qry))
	{
		file_put_contents($GLOBALS['mysql_failed_inserts'], $qry, FILE_APPEND); 
		shell_exec("chown www-data:www-data " . $GLOBALS['mysql_failed_inserts']);
	}
}


function check_msg_already_sent($channel, $title, $msg, $date){
	$qry = mysqli_query($GLOBALS['conn'], "
		select * 
		from t_log_message
		where
			date_sent > '" . $date . "'
			and channel = '" . my_escape_string($channel) . "'
			and title = '" . my_escape_string($title) . "'
			and message = '" . my_escape_string($msg) . "'
		");
	return mysqli_num_rows($qry) > 0;
}

function send_msg($channel, $title, $msg, $priority = 0, $send_by = 'include'){
	$response = [];
	
	$result_nma = 0;
	$result_ortc = 0;
	//$send_by = '';

	if($channel != '' && $msg != ''){
		/*
		if($title == ''){
			$title = $channel;
		}
		
		if($nma->verify()){
			if($nma->notify($channel, $title, $msg, $priority)){
				if(!$by_shell){
					echo "NMA notifcation sent!" . " <br>\n";
				}
			}
			else {
				echo "NMA notifcation not sent!" . " <br>\n";
			}
		}
		*/
		if($GLOBALS['enable_nma'] == 1){
			$result_nma = send_nma_msg($channel, $title, $msg, $priority) ? 2 : 0;
			$send_by = 'NMA,' . $send_by;
		}
		/*if(!$by_shell){
			echo 'NMA response:'. $result_nma . " <br>\n";
			//print_r($response);
		}*/
		
		if($GLOBALS['enable_ortc'] == 1){
			$result_ortc = send_ortc_msg($channel, $msg, $response) ? 1 : 0;
			$send_by = 'ORTC,' . $send_by;
		}
		/*if(!$by_shell){
			echo 'ORTC response:'. $result_ortc . " <br>\n";
			print_r($response);
		}*/
		
	}
	/*else {
		echo 'message or channel not set';
	}*/

	log_message($send_by, $GLOBALS['messages_host'], $channel, $title, $msg, $result_nma + $result_ortc);
}
?>