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
	
	mysql_query("
		update t_setting
		set
			value = '" . mysql_real_escape_string($value) . "'
			
		where
			id_app = " . $id_app . "
			and code = '" . mysql_real_escape_string($code) . "'
			and active = 1
			
		", $conn_users);
		
}

?>