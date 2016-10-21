<?php

//require 'appinit.php';

$color = '00ff00';

$logo = '/var/www/_assets/images/dome.png';
$logo_dir = '/var/docs/localdome_assets/logo/';
$logo_name = 'logo_' . $color . '.png';

// create directory if not exists
$parts = explode('/', $logo_dir, -1);
$dir = '';
foreach($parts as $part){
	if(!is_dir($dir .= "/$part")) mkdir($dir);
}


if(!file_exists($logo_dir) . $logo_name)
{
	
	$image = imagecreatefrompng($logo);
	
	imagefill($image, 160, 30, hexdec($color));
	imagefill($image, 230, 30, hexdec($color));
	
	imagefill($image, 100, 70, hexdec($color));
	imagefill($image, 200, 70, hexdec($color));
	imagefill($image, 300, 70, hexdec($color));
	
	imagefill($image, 60, 120, hexdec($color));
	imagefill($image, 150, 120, hexdec($color));
	imagefill($image, 220, 120, hexdec($color));
	imagefill($image, 340, 120, hexdec($color));
	
	imagefill($image, 70, 170, hexdec($color));
	imagefill($image, 190, 170, hexdec($color));
	imagefill($image, 350, 170, hexdec($color));

	imagepng($image, $logo_dir . $logo_name);

}

header("Content-Type: image/png");
header('Content-disposition: inline; filename="' . $logo_name . '"'); 

header('Cache-control: max-age=' . (60*60*24*30));
header('Expires: ' . gmdate(DATE_RFC1123, time()+60*60*24*30));

readfile($logo_dir . $logo_name);

?>