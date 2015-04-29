<?php
//set headers to NOT cache a page
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT"); // Date in the past

//or, if you DO want a file to cache, use:
//header("Cache-Control: max-age=2592000"); //30days (60sec * 60min * 24hours * 30days)

$extarr = explode('.', $_GET['f']);
$extension = $extarr[count($extarr) - 1];
switch(strtolower($extension)){
	case 'css':
		header("Content-Type: text/css");
		break;
		
	case 'jpg':
	case 'png':
	case 'gif':
		header("Content-Type: image/" . strtolower($extension));
		break;
	
	default:
		header("Content-Type: text/html");
}

echo file_get_contents($_GET['f']);

?>