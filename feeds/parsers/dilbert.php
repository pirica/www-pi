<?php

$str = file_get_contents($feeds['url']);

$tmp = explode('img-comic-container', $str, 2);

if(count($tmp) > 1){
	
	$date = substr($tmp[1], strpos($tmp[1], 'href="')+6, strpos($tmp[1], '"', strpos($tmp[1], 'href="') + 6) - strpos($tmp[1], 'href="') - 6);
	$date = explode('/', $date);
	$date = $date[count($date) - 1];
	
	$tmp = explode('<img', $tmp[1]);
	$tmp = explode('>', $tmp[1])[0];
	//$tmp = explode(' ', $tmp[0]);
	
	$title = $date . ' - ' . substr($tmp, strpos($tmp, 'alt="')+5, strpos($tmp, '"', strpos($tmp, 'alt="') + 5) - strpos($tmp, 'alt="') - 5);
	
	$description = '<img src="' . substr($tmp, strpos($tmp, 'src="')+5, strpos($tmp, '"', strpos($tmp, 'src="') + 5) - strpos($tmp, 'src="') - 5) . '">';
	
}


?>