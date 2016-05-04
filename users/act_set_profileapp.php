<?php
$id_profile = saneInput('id_profile', 'int', -1);
$id_app = saneInput('id_app', 'int', -1);
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

if(!in_array($field, array('allowed'))){
	$field = '';
}

$error = 0;
if($id_app > 0 && $id_profile > 0 && $field != ''){
	
	$qry_save_profile_app = $mysqli->prepare("
		update t_profile_app
		set
			" . $field . " = ?
			
		where
			id_app = ?
			and id_profile = ?
			and active = 1
			
		");
		
	$qry_save_profile_app->bind_param('sii', $value, $id_app, $id_profile);
	$qry_save_profile_app->execute();
	
}

?>