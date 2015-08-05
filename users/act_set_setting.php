<?php
$id_app = saneInput('id_app', 'int', -1);
$code = saneInput('code', 'string');
$value = saneInput('value', 'string');
$edittype = saneInput('edittype', 'string', 'string');

$error = 0;
if(/*$id_app > 0 &&*/ $code != ''){
	
	switch($edittype){
		case 'size':
		case 'filesize':
			$value = revertFileSize($value);
			break;
	}
	
	$qry_save_setting = $mysqli->prepare("
		update t_setting
		set
			value = ?
			
		where
			id_app = ?
			and code = ?
			and active = 1
			
		");
		
	$qry_save_setting->bind_param('sis', $value, $id_app, $code);
	$qry_save_setting->execute();
	
}

?>