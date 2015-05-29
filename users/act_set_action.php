<?php
$id_app = saneInput('id_app', 'int', -1);
$code = saneInput('code', 'string');
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

if(!in_array($field, array('page_title', 'login_required'))){
	$field = '';
}

$error = 0;
if($id_app > 0 && $code != ''){
	
	
	$qry_save_action = $mysqli->prepare("
		update t_app_action
		set
			" . $field . " = ?
			
		where
			id_app = ?
			and code = ?
			and active = 1
			
		");
		
	$qry_save_action->bind_param('sis', $value, $id_app, $code);
	$qry_save_action->execute();
	
}

?>