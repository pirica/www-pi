<?php
$id_app = saneInput('id_app', 'int', 0);
$code = saneInput('code', 'string');
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

if(!in_array($field, array('page_title', 'login_required'))){
	$field = '';
}

$error = 0;
if(($id_app > 0 || $id_app == -1) && $code != ''){
	
	
	mysql_query("
		update t_app_action
		set
			" . $field . " = '" . mysql_real_escape_string($value) . "'
			
		where
			ifnull(id_app,-1) = " . $id_app . "
			and code = '" . mysql_real_escape_string($code) . "'
			and active = 1
			
		", $conn_users);
		
}

?>