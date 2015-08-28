<?php
require 'connection.php';
require '../_core/functions.php';

$src = saneInput('src', 'string', '');
$fa = explode('/',$src);
$filename = array_pop($fa);
header("Content-Type: video/mp4");
header('Content-disposition: inline; filename="' . $filename . '"'); 
readfile($main_dir . $src);
?>