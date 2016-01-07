<?php

$crondate = time();

if(date("H", $crondate) > 10 && date("H", $crondate) < 17){

	$str = file_get_contents('http://www.vandenborre.be/status-bestelling?Token=' . $tt['tracking_code']);
	//echo $str;
	//echo "\n\n==================\n\n";


	$tmp = explode('<tr', $str);

	if(count($tmp) > 2){
		$tmp = explode('<td', $tmp[2]);
		
		if(count($tmp) > 5){
			$msg = $tmp[5];
			$msg = str_replace('<br>', ' ', $msg);
			$msg = str_replace('<br/>', ' ', $msg);
			$msg = str_replace('<br />', ' ', $msg);
			$msg = strip_tags($msg);
		}
		
	}

}

?>