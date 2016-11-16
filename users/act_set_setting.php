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
	
	mysqli_query($conn_users, "
		update t_setting
		set
			value = '" . mysqli_real_escape_string($conn_users, $value) . "'
			
		where
			id_app = " . $id_app . "
			and code = '" . mysqli_real_escape_string($conn_users, $code) . "'
			and active = 1
			
		");
		
	$settings->clearCache();
}

?>