<?php
$debug = 0;
if(isset($_GET['debug']) && $_GET['debug'] == 1){
	$debug = 1;
}
//require 'connection.php';
require 'functions.php';
require dirname(__FILE__).'/../_core/appinit.php';


require 'com/Email_Reader.php';
$emailhandle = New Email_reader(
	$settings->val('email_hostname', ''),
	$settings->val('email_username', ''),
	$settings->val('email_password', ''),
	$settings->val('email_port', 110)
);
$emails = $emailhandle->inbox();

/*
hotmail:
$emailhandle = New Email_reader(
	'pop3.live.com',
	'wim_fleerackers@hotmail.com',
	'6twRYN2u',
	995
);
*/

/*
include('com/pop3.php');
$rPOP3handle = new pop3('mail.wikke.net','wikke@wikke.net','passw');
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
	
	$email_info = print_r($email,true);
	
	$fromaddress = '- not set -';
	$subject = '- not set -';
	$date = '- not set -';
	$message_id = '- not set -';
	$toaddress = '- not set -';
	
	if(isset($email['header']->fromaddress)){
		$fromaddress = $email['header']->fromaddress;
	}
	if(isset($email['header']->subject)){
		$subject = $email['header']->subject;
	}
	if(isset($email['header']->date)){
		$date = $email['header']->date;
	}
	if(isset($email['header']->message_id)){
		$message_id = $email['header']->message_id;
	}
	if(isset($email['header']->toaddress)){
		$toaddress = $email['header']->toaddress;
	}
	
	/*
		[body]
	   [structure] => stdClass Object
                (
                    [type] => 0
                    [encoding] => 0
                    [ifsubtype] => 1
                    [subtype] => HTML
                    [ifdescription] => 0
                    [ifid] => 0
                    [lines] => 652
                    [bytes] => 53821
                    [ifdisposition] => 0
                    [ifdparameters] => 0
                    [ifparameters] => 1
                    [parameters] => Array
                        (
                            [0] => stdClass Object
                                (
                                    [attribute] => CHARSET
                                    [value] => utf-8
                                )

                        )

                )
	*/
	
	
	// LOGGING
	
	$id_email = '';
	$id_emails = ',';
	
	$qry_check = mysql_query("
		select * from t_email
		where
			ifnull(fromaddress,'- not set -') = '" . mysql_real_escape_string($fromaddress) . "'
			and ifnull(subject,'- not set -') = '" . mysql_real_escape_string($subject) . "'
			and ifnull(date,'- not set -') = '" . mysql_real_escape_string($date) . "'
			and ifnull(message_id,'- not set -') = '" . mysql_real_escape_string($message_id) . "'
			and ifnull(toaddress,'- not set -') = '" . mysql_real_escape_string($toaddress) . "'
		");
		
	while($check = mysql_fetch_array($qry_check)){
		$id_emails .= $check['id_email'] . ',';
	}
	
	if(mysql_num_rows($qry_check) == 0){
		
		$db_fromaddress	= $fromaddress	== '- not set -' ? "NULL" : "'" . mysql_real_escape_string($fromaddress) . "'";
		$db_subject		= $subject		== '- not set -' ? "NULL" : "'" . mysql_real_escape_string($subject) . "'";
		$db_date		= $date			== '- not set -' ? "NULL" : "'" . mysql_real_escape_string($date) . "'";
		$db_message_id	= $message_id	== '- not set -' ? "NULL" : "'" . mysql_real_escape_string($message_id) . "'";
		$db_toaddress	= $toaddress	== '- not set -' ? "NULL" : "'" . mysql_real_escape_string($toaddress) . "'";
		
		mysql_query("
			insert into t_email
			(
				raw,
				fromaddress,
				subject,
				date,
				message_id,
				toaddress,
				body
			)
			values
			(
				'" . mysql_real_escape_string($email_info) . "',
				" . $db_fromaddress . ",
				" . $db_subject . ",
				" . $db_date . ",
				" . $db_message_id . ",
				" . $db_toaddress . ",
				'" . mysql_real_escape_string($email['body']) . "'
			)
			");
		$id_email = mysql_insert_id($conn);
		$id_emails .= $id_email . ',';
	}
	
	
	// SPAM PROTECTION
	
	$qry = mysql_query("
		select
			id_email_spam,
			when_subject,
			when_from,
			when_to,
			when_body
			
		from t_email_spam
		where
			enabled = 1
			and (
				(ifnull(when_subject,'') <> '' and '" . mysql_real_escape_string($subject) . "' like when_subject)
				or
				(ifnull(when_from,'') <> '' and '" . mysql_real_escape_string($fromaddress) . "' like when_from)
				or
				(ifnull(when_to,'') <> '' and '" . mysql_real_escape_string($toaddress) . "' like when_to)
				or
				(ifnull(when_to,'') <> '' and '" . mysql_real_escape_string($toaddress) . "' like concat('%<', when_to, '>%') )
				or
				(ifnull(when_body,'') <> '' and '" . mysql_real_escape_string(base64_decode($email['body'])) . "' like when_body)
			)
		");

	while($spam = mysql_fetch_array($qry)){
		$emailhandle->markRemove($email['index']);
		
		mysql_query("update t_email_spam set last_hit = now() where id_email_spam = " . $spam['id_email_spam']);
		
		if($id_emails != ','){
			mysql_query("update t_email set is_spam = 1 where id_email in (0" . $id_emails . "0) and is_spam = 0");
		}
	}
	
	
	
	// ALERTING
	
	/*
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
				(ifnull(when_from,'') <> '' and '" . mysql_real_escape_string($fromaddress) . "' like when_from)
				or
				(ifnull(when_subject,'') <> '' and '" . mysql_real_escape_string($subject) . "' like when_subject)
			)
		");

	while($tt = mysql_fetch_array($qry)){
		$channel = 'Email_' . $tt['description'];
		$title =  'Email from ' . $fromaddress;
		$msg = 'Sub: ' . $subject;
		$priority = $settings->val('email_alerting_priority', 2);
		
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
	*/
}

$emailhandle->remove();



// ALERTING


$qry = mysql_query("
	select
		ae.id_alert_email,
		ae.description,
		ae.when_from,
		ae.when_subject,
		
		e.fromaddress,
		e.subject
		
	from t_alert_email ae
	join t_email e on e.is_alerted = 0
		and (
			(ifnull(ae.when_from,'') <> '' and e.fromaddress like ae.when_from)
			or
			(ifnull(ae.when_subject,'') <> '' and e.subject like ae.when_subject)
		)
	where
		ae.enabled = 1
	");

while($tt = mysql_fetch_array($qry)){
	$channel = 'Email_' . $tt['description'];
	$title =  'Email from ' . $tt['fromaddress'];
	$msg = 'Sub: ' . $tt['subject'];
	$priority = $settings->val('email_alerting_priority', 2);
	
	$status = '';
	/*
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
		
	while($ttresult = mysql_fetch_array($qry_result)){
		$status = $ttresult['result'];
	}
	*/
	
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

mysql_query("update t_email set is_alerted = 1 where is_alerted = 0");




// TRACK & TRACE


$qry = mysql_query("
	select
		ttt.id_tracktrace_type,
		ttt.tracking_code,
		e.fromaddress,
		e.subject,
		e.body
		
	from t_tracktrace_type ttt
	join t_email e on e.is_tracktrace = 0
		 and e.body like concat('%', ttt.tracking_code , '%')
	where
		ifnull(ttt.tracking_code,'') <> ''
	");

while($tt = mysql_fetch_array($qry)){
	$tmp = $tt['body'];
	$tmp = $tt['tracking_code'] . explode($tt['tracking_code'], $tmp, 2)[1];
	$tmp = explode('&', $tmp, 2)[0];
	$tmp = explode(' ', $tmp, 2)[0];
	$tmp = explode('.', $tmp, 2)[0];
	$tmp = explode(',', $tmp, 2)[0];
	
	if($tmp != ''){
		
		$postalcode = '2440';
		if(strpos($tt['body'], '2630') > 0){
			$postalcode = '2630';
		}
		
		$added = false;
		
		$qry2 = mysql_query("
			select
				tt.*
			from t_tracktrace
			where
				tt.tracking_code = '" . mysql_real_escape_string($tmp) . "'
			");
		
		while($ttcheck = mysql_fetch_array($qry2)){
			$added = true;
		}
		
		if(!$added){
			mysql_query("
				insert into t_tracktrace
				(
					id_tracktrace_type,
					enabled,
					tracking_code,
					postal_code,
					title
				)
				values
				(
					" . $tt['id_tracktrace_type'] . ",
					1,
					'" . mysql_real_escape_string($tmp) . "',
					'" . mysql_real_escape_string($postalcode) . "',
					'" . mysql_real_escape_string($tt['fromaddress']) . "'
				)
				");
		}
	}
}

mysql_query("update t_email set is_tracktrace = 1 where is_tracktrace = 0");




?>