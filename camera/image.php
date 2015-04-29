<?php
require 'connection.php';
require '../_core/functions.php';

$src = saneInput('src', 'string', '');
header("Content-Type: image/jpeg");
header('Content-disposition: inline; filename="' . array_pop(explode('/',$src)) . '"'); 
readfile($main_dir . $src);
?>