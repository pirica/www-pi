<?php
$id_host = saneInput('id_host', 'int', -1);
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

$error = 0;
if($id_host > 0 && $field != ''){
	mysql_query("
		update t_host
		set
			" . $field . " = '" . mysql_real_escape_string($value) . "'
			
		where
			id_host = " . $id_host . "
		");
}

?>