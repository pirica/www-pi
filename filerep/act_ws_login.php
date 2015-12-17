<?php
include 'act_settings.php';

$email = mysql_real_escape_string(saneInput('email'));
$password = mysql_real_escape_string(saneInput('password'));

$qry = mysql_query("
	select
		u.id_user,
		u.username,
		u.email,
		u.date_inserted,
		u.id_profile	
	
	from users.t_user u
	where
		u.email = '" . $email . "'
		and u.password = '" . $password . "'
		and u.active = 1
		
	", $conn);
	
$returnvalue = array('data' => mysql2json($qry));

?>