<?php
require 'connection.php';
require '../_core/functions.php';

$src = saneInput('src', 'string', '');
$fa = explode('/',$src);
$filename = array_pop($fa);
header("Content-Type: image/jpeg");
header('Content-disposition: inline; filename="' . $filename . '"'); 

header('Cache-control: max-age='.(60*60*24*30));
header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*30));

if(file_exists($main_dir . $src)){
	readfile($main_dir . $src);
}
else {
	readfile($archive_dir . $src);
}
?>