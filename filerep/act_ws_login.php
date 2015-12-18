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
		u.id_profile,
		u.password,
		u.salt
	
	from users.t_user u
	where
		u.email = '" . $email . "'
		and u.active = 1
		
	", $conn);
	
$user = mysql_fetch_array($qry);
$password_check = hash('sha512', $password . $user['salt']);

if($password_check == $user['password']){
	$returnvalue = array('type' => 'ok', 'data' => mysql2json($user));
}
else {
	$returnvalue = array('type' => 'nok', 'message' => 'login failed');
}
	


?>