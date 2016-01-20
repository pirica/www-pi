<?php

$str = file_get_contents($feeds['url']);

$tmp = explode('id="main-comic"', $str, 2);

if(count($tmp) > 1){
	$tmp = $tmp[1];
	
	$description = substr($tmp, strpos($tmp, 'src="')+5, strpos($tmp, '"', strpos($tmp, 'src="') + 5) - strpos($tmp, 'src="') - 5);
	
	$tmptitle = explode('/', $description);
	$tmptitle = $tmptitle[count($tmptitle) - 1];
	
	$title = date('Y-m-d', time()) . ' - ' . $tmptitle;
	
	$description = '<img src="' . $description . '">';
	
}


?>