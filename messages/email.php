<?php
$debug = 0;
if(isset($_GET['debug']) && $_GET['debug'] == 1){
	$debug = 1;
}

require dirname(__FILE__).'/../_core/appinit.php';

require 'functions.php';

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
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
		
		$qry_check = mysqli_query($conn, "
			select * from t_email
			where
				ifnull(fromaddress,'- not set -') = '" . mysqli_real_escape_string($conn, $fromaddress) . "'
				and ifnull(subject,'- not set -') = '" . mysqli_real_escape_string($conn, $subject) . "'
				and ifnull(date,'- not set -') = '" . mysqli_real_escape_string($conn, $date) . "'
				and ifnull(message_id,'- not set -') = '" . mysqli_real_escape_string($conn, $message_id) . "'
				and ifnull(toaddress,'- not set -') = '" . mysqli_real_escape_string($conn, $toaddress) . "'
			");
			
		while($check = mysqli_fetch_array($qry_check)){
			$id_emails .= $check['id_email'] . ',';
		}
		
		if(mysqli_num_rows($qry_check) == 0){
			
			$db_fromaddress	= $fromaddress	== '- not set -' ? "NULL" : "'" . mysqli_real_escape_string($conn, $fromaddress) . "'";
			$db_subject		= $subject		== '- not set -' ? "NULL" : "'" . mysqli_real_escape_string($conn, $subject) . "'";
			$db_date		= $date			== '- not set -' ? "NULL" : "'" . mysqli_real_escape_string($conn, $date) . "'";
			$db_message_id	= $message_id	== '- not set -' ? "NULL" : "'" . mysqli_real_escape_string($conn, $message_id) . "'";
			$db_toaddress	= $toaddress	== '- not set -' ? "NULL" : "'" . mysqli_real_escape_string($conn, $toaddress) . "'";
			
			mysqli_query($conn, "
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
					'" . mysqli_real_escape_string($conn, $email_info) . "',
					" . $db_fromaddress . ",
					" . $db_subject . ",
					" . $db_date . ",
					" . $db_message_id . ",
					" . $db_toaddress . ",
					'" . mysqli_real_escape_string($conn, $email['body']) . "'
				)
				");
			$id_email = mysqli_insert_id($conn);
			$id_emails .= $id_email . ',';
		}
		
		
		// SPAM PROTECTION
		
		$qry = mysqli_query($conn, "
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
					(ifnull(when_subject,'') <> '' and '" . mysqli_real_escape_string($conn, $subject) . "' like when_subject)
					or
					(ifnull(when_from,'') <> '' and '" . mysqli_real_escape_string($conn, $fromaddress) . "' like when_from)
					or
					(ifnull(when_to,'') <> '' and '" . mysqli_real_escape_string($conn, $toaddress) . "' like when_to)
					or
					(ifnull(when_to,'') <> '' and '" . mysqli_real_escape_string($conn, $toaddress) . "' like concat('%<', when_to, '>%') )
					or
					(ifnull(when_body,'') <> '' and '" . mysqli_real_escape_string($conn, base64_decode($email['body'])) . "' like when_body)
				)
			");

		while($spam = mysqli_fetch_array($qry)){
			$emailhandle->markRemove($email['index']);
			
			mysqli_query($conn, "update t_email_spam set last_hit = now() where id_email_spam = " . $spam['id_email_spam']);
			
			if($id_emails != ','){
				mysqli_query($conn, "update t_email set is_spam = 1 where id_email in (0" . $id_emails . "0) and is_spam = 0");
			}
		}
		
	}

	$emailhandle->remove();



	// ALERTING


	$qry = mysqli_query($conn, "
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
			and e.is_spam = 0
		where
			ae.enabled = 1
		");

	while($tt = mysqli_fetch_array($qry)){
		$channel = 'Email_' . $tt['description'];
		$title =  'Email from ' . $tt['fromaddress'];
		$msg = 'Sub: ' . $tt['subject'];
		$priority = $settings->val('email_alerting_priority', 2);
		
		$status = '';
		
		if($status == ''){
			
			mysqli_query($conn, "
				insert into t_alert_email_result
				(
					id_alert_email,
					result,
					date_result
				)
				values
				(
					" . $tt['id_alert_email'] . ",
					'" . mysqli_real_escape_string($conn, $title . ' - ' . $msg) . "',
					now()
				)
				");
		
			send_msg($channel, $title, $msg, $priority);
			
		}
	}

	mysqli_query($conn, "update t_email set is_alerted = 1 where is_alerted = 0");




	// TRACK & TRACE

	$qry = mysqli_query($conn, "
		select
			ttt.id_tracktrace_type,
			ttt.tracking_code,
			e.id_email,
			e.fromaddress,
			e.subject,
			e.body
			
		from t_tracktrace_type ttt
		join t_email e on e.is_tracktrace = 0
			and e.body like concat('%', ttt.tracking_code , '%')
			and e.body like concat('%', ttt.description , '%')
			and e.is_spam = 0
		where
			ifnull(ttt.tracking_code,'') <> ''
		");

		
	while($tt = mysqli_fetch_array($qry)){
		$tmp = $tt['body'];
		$tmp = $tt['tracking_code'] . explode($tt['tracking_code'], $tmp, 2)[1];
		$tmp = explode('&', $tmp, 2)[0];
		$tmp = explode(' ', $tmp, 2)[0];
		$tmp = explode('.', $tmp, 2)[0];
		$tmp = explode(',', $tmp, 2)[0];
		$tmp = explode('"', $tmp, 2)[0];
		/*
		if($tmp != ''){
			
			
			$postalcode = '2440';
			if(strpos($tt['body'], '2630') > 0){
				$postalcode = '2630';
			}
			
			if(strpos($codes, ',' . $tmp . ',') === false){
				mysqli_query($conn, "
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
						'" . mysqli_real_escape_string($conn, $tmp) . "',
						'" . mysqli_real_escape_string($conn, $postalcode) . "',
						'" . mysqli_real_escape_string($conn, $tt['fromaddress']) . "'
					)
					");
			}
		}
		*/
		mysqli_query($conn, "update t_email set is_tracktrace = 1, tracking_code = '" . mysqli_real_escape_string($conn, $tmp) . "' where id_email = " . $tt['id_email']);
		
	}

	mysqli_query($conn, "
		insert into t_tracktrace
		(
			id_tracktrace_type,
			enabled,
			tracking_code,
			postal_code,
			title
		)
		select
			ttt.id_tracktrace_type,
			1,
			e.tracking_code,
			case when e.body like '%2630%' then '2630' else '2440' end,
			e.fromaddress
		from t_email e
		join t_tracktrace_type ttt on e.tracking_code like concat(ttt.tracking_code, '%')
			and ifnull(ttt.tracking_code,'') <> ''
		left join t_tracktrace tt on tt.tracking_code = e.tracking_code
		where ifnull(e.tracking_code,'') <> ''
			and tt.id_tracktrace is null
		limit 1
		");

	$task->setIsRunning(false);
	
}


?>