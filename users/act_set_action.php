<?php
$id_app = saneInput('id_app', 'int', 0);
$code = saneInput('code', 'string');
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

if(!in_array($field, array('page_title', 'login_required', 'show_in_menu', 'sort_order'))){
	$field = '';
}

$error = 0;
if(($id_app > 0 || $id_app == -1) && $code != ''){
	
	
	mysqli_query($conn_users, "
		update t_app_action
		set
			" . $field . " = '" . mysqli_real_escape_string($conn_users, $value) . "'
			
		where
			ifnull(id_app,-1) = " . $id_app . "
			and code = '" . mysqli_real_escape_string($conn_users, $code) . "'
			and active = 1
			
		");
		
}

?>