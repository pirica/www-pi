<?php
require 'connections.php';
//require '../_core/functions.php';
require '../_core/webinit.php';


$src = saneInput('src', 'string', '');
$fa = explode('/',$src);
$filename = array_pop($fa);
header("Content-Type: image/jpeg");
header('Content-disposition: inline; filename="' . $filename . '"'); 

header('Cache-control: max-age='.(60*60*24*30));
header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*30));

readfile($settings->val('photos_path', '') . $src);

?>