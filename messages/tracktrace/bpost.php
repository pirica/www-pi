<?php

$str = file_get_contents('http://track.bpost.be/etr/light/performSearch.do?searchByItemCode=true&oss_language=NL&itemCodes=' . $tt['tracking_code']);

//$str = str_replace('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">', '', $str);
//$str = '<html' . explode('<html', $str, 2)[1];

//echo $str;
//echo "\n\n==================\n\n";


$tmp = explode($tt['tracking_code'], $str);
if(count($tmp) > 4)
{
	$str = $tmp[4];
	$msg = explode('</tr>', $str, 2)[0];
	
	//echo $msg;
	//echo "\n\n==================\n\n";
	
	$msg = strip_tags($msg);
	
	//echo $msg;
	//echo "\n\n==================\n\n";

	$msg = str_replace("\r", ' ', $msg);
	$msg = str_replace("\n", ' ', $msg);
	$msg = str_replace("\t", ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);
	$msg = str_replace('  ', ' ', $msg);

	if($msg != $status){
		$status_changed = true;
		
	}
}

?>