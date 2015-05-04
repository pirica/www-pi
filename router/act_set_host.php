<?php
$id_host = saneInput('id_host', 'int', -1);
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

$error = 0;
if($id_host > 0 && $field != ''){
	
	switch($field){
		case 'alert_when_traffic_exceeds_daily':
		case 'alert_when_traffic_exceeds_monthly':
			
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
			
			$newvalue *= $value;
			
			break;
	}
	
	mysql_query("
		update t_host
		set
			" . $field . " = '" . mysql_real_escape_string($value) . "'
			
		where
			id_host = " . $id_host . "
		");
}

?>