<?php
$debug = 0;
require 'connection.php';
require 'functions.php';


require 'com/Email_Reader.php';
$emailhandle = New Email_reader();
$emails = $emailhandle->inbox();

/*
include('com/pop3.php');
$rPOP3handle = new pop3('mail.wikke.net','wikke@wikke.net','8eNAdruv');
//if($debug == 1) print_r($rPOP3handle->retrieveMessage(1));

$list = $rPOP3handle->listMessages();
if($debug == 1) print_r($list);

$emails = [];

$total = count($list);
for($i=0; $i<$total; $i++) {
    $emails[] = $rPOP3handle->retrieveMessage($i, true);
}

*/

if($debug == 1) print_r($emails);


$total = count($emails);
for($i=$total-1;$i>=0;$i--) {
	//$email = $emails->inbox[$i];
	$email = $emails[$i];
	
	if($debug == 1) print_r($email);
	
	
	$qry = mysql_query("
		select
			id_alert_email,
			description,
			when_from,
			when_subject
			
		from t_alert_email
		where
			enabled = 1
			and (
				(ifnull(when_from,'') <> '' and '" . mysql_real_escape_string($email['header']->fromaddress) . "' like when_from)
				or
				(ifnull(when_subject,'') <> '' and '" . mysql_real_escape_string($email['header']->subject) . "' like when_subject)
			)
		");

	while($tt = mysql_fetch_array($qry)){
		$channel = 'Email_' . $tt['description'];
		$title =  'Email from ' . $email['header']->fromaddress;
		$msg = 'Sub: ' . $email['header']->subject;
		$priority = 2;
		
		$qry_result = mysql_query("
			select 
				result
			from t_alert_email_result
			where
				id_alert_email = " . $tt['id_alert_email'] . "
				and result = '" . mysql_real_escape_string($title . ' - ' . $msg) . "'
				#and date_result < now() - interval 1 hour
			order by
				id_alert_email_result desc
			limit 1
			");
			
		$status = '';
		while($ttresult = mysql_fetch_array($qry_result)){
			$status = $ttresult['result'];
		}
		
		if($status == ''){
			
			mysql_query("
				insert into t_alert_email_result
				(
					id_alert_email,
					result,
					date_result
				)
				values
				(
					" . $tt['id_alert_email'] . ",
					'" . mysql_real_escape_string($title . ' - ' . $msg) . "',
					now()
				)
				");
		
			send_msg($channel, $title, $msg, $priority);
			
		}
	}
}

?>