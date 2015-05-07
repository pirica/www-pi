<?php
$id_app = saneInput('id_app', 'int', -1);
$code = saneInput('code', 'string');
$value = saneInput('value', 'string');
$edittype = saneInput('edittype', 'string', 'string');

$error = 0;
if($id_app > 0 && $code != ''){
	
	switch($edittype){
		case 'size':
		case 'filesize':
			
			$newvalue = 1;
			$value = strtolower($value);
			
			if(strpos($value, 't') !== false){
				$newvalue = 1024 * 1024 * 1024 * 1024;
				$value = str_replace('t', '', $value);
			}
			else if(strpos($value, 'g') !== false){
				$newvalue = 1024 * 1024 * 1024;
				$value = str_replace('g', '', $value);
			}
			else if(strpos($value, 'm') !== false){
				$newvalue = 1024 * 1024;
				$value = str_replace('m', '', $value);
			}
			else if(strpos($value, 'k') !== false){
				$newvalue = 1024;
				$value = str_replace('k', '', $value);
			}
			
			$value *= $newvalue;
			
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