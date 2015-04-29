<?php

$str = file_get_contents('http://wwwapps.ups.com/ietracking/tracking.cgi?TypeOfInquiryNumber=T&InquiryNumber1=' . $tt['tracking_code']);
//echo $str;
//echo "\n\n==================\n\n";


$tmp = explode($tt['tracking_code'], $str);
if(count($tmp) > 5)
{
	$str = $tmp[5];
	$tmp = explode('pkgstep current', $str);
	
	$msg = $tmp[count($tmp) - 1];
	
	$msg = explode('</div>', $msg)[0];
	
	//echo $msg;
	//echo "\n\n==================\n\n";
	
	$msg = strip_tags($msg);
	
	//echo $msg;
	//echo "\n\n==================\n\n";

	$msg = str_replace("\r", ' ', $msg);
	$msg = str_replace("\n", ' ', $msg);
	$msg = str_replace("\t", ' ', $msg);
	$msg = str_replace('&nbsp;', ' ', $msg);
	$msg = str_replace('">', ' ', $msg);
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