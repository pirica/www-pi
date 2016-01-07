<?php

$crondate = time();

if(
	(date("H", $crondate) > 10 && date("H", $crondate) < 17) && 
	(date("w", $crondate) != 0 && date("w", $crondate) != 6) // not in weekends
){

	$str = file_get_contents('http://www.vandenborre.be/status-bestelling?Token=' . $tt['tracking_code']);
	//echo $str;
	//echo "\n\n==================\n\n";


	$tmp = explode('<tr', $str);

	if(count($tmp) > 3){
		$tmp = explode('<td', $tmp[3]);
		
		if(count($tmp) > 6){
			
			$msg = explode('>', $tmp[6], 2)[1];
			
			$msg = str_replace('<br>', ' ', $msg);
			$msg = str_replace('<br/>', ' ', $msg);
			$msg = str_replace('<br />', ' ', $msg);
			$msg = strip_tags($msg);
			
			$msg = str_replace("\r", ' ', $msg);
			$msg = str_replace("\n", ' ', $msg);
			$msg = str_replace("\t", ' ', $msg);
			
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
			$msg = str_replace('  ', ' ', $msg);
		}
		
	}

}

?>