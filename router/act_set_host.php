<?php
$id_host = saneInput('id_host', 'int', -1);
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

$error = 0;
if($id_host > 0 && $field != ''){
	
	switch($field){
		case 'alert_when_traffic_exceeds_daily':
		case 'alert_when_traffic_exceeds_monthly':
			
			echo "recalc\n";
			
			$newvalue = 1;
			$value = strtolower($value);
			
			echo "value:".$value."\n";
			
			if(strpos($value, 't') !== false){
				$newvalue = 1024 * 1024 * 1024 * 1024;
				$value = str_replace('t', '', $value);
				
				echo "T\n";
				echo "value:".$value."\n";
				echo "newvalue:".$newvalue."\n";
				
			}
			else if(strpos($value, 'g') !== false){
				$newvalue = 1024 * 1024 * 1024;
				$value = str_replace('g', '', $value);
				
				echo "G\n";
				echo "value:".$value."\n";
				echo "newvalue:".$newvalue."\n";
				
			}
			else if(strpos($value, 'm') !== false){
				$newvalue = 1024 * 1024;
				$value = str_replace('m', '', $value);
				
				echo "M\n";
				echo "value:".$value."\n";
				echo "newvalue:".$newvalue."\n";
				
			}
			else if(strpos($value, 'k') !== false){
				$newvalue = 1024;
				$value = str_replace('k', '', $value);
				
				echo "K\n";
				echo "value:".$value."\n";
				echo "newvalue:".$newvalue."\n";
				
			}
			
			$value *= $newvalue;
			
			echo "value:".$value."\n";
			
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