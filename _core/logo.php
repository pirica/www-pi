<?php

//require 'appinit.php';

$width = 400;
$color = '00ff00';
$bgcolor = '222222';

if(isset($_GET['width']) && $_GET['width'] != '' && is_numeric($_GET['width']) && $_GET['width'] > 0){
	$width = $_GET['width'];
}
if(isset($_GET['color']) && $_GET['color'] != '' && (strlen($_GET['color']) == 6 || strlen($_GET['color']) == 3) && ctype_xdigit($_GET['color'])){
	$color = $_GET['color'];
}
if(isset($_GET['bgcolor']) && $_GET['bgcolor'] != '' && (strlen($_GET['bgcolor']) == 6 || strlen($_GET['bgcolor']) == 3) && ctype_xdigit($_GET['bgcolor'])){
	$bgcolor = $_GET['bgcolor'];
}

$logo_dir = '/var/docs/localdome_assets/logo/';
$logo_name = 'logo_' . $color . '_' . $bgcolor . '_' . $width . '.png';

// create directory if not exists
$parts = explode('/', $logo_dir, -1);
$dir = '';
foreach($parts as $part){
	if(!is_dir($dir .= "/$part")) mkdir($dir);
}

if(!file_exists($logo_dir) . $logo_name)
{
	
	$color = hexdec($color);
	$bgcolor = hexdec($bgcolor);
	
	$image = imagecreatetruecolor(400, 200);
	imagefilledrectangle($image, 0, 0, 400, 200, $bgcolor);
	
	//imageantialias($image, true);
	
	imagefilledpolygon($image, array(101,42,	141,22,		187,15,		187,42), 4, $color);
	imagefilledpolygon($image, array(201,15,	259,22,		295,42,		201,42), 4, $color);
	
	imagefilledpolygon($image, array(84,55,		140,55,		140,95,		51,95), 4, $color);
	imagefilledpolygon($image, array(152,55,	237,55,		237,95,		152,95), 4, $color);
	imagefilledpolygon($image, array(255,55,	310,55,		342,95,		255,95), 4, $color);
	
	imagefilledpolygon($image, array(45,107,	77,107,		77,142,		27,142), 4, $color);
	imagefilledpolygon($image, array(91,107,	186,107,	186,142,	91,142), 4, $color);
	imagefilledpolygon($image, array(200,107,	303,107,	303,130,	294,123,	283,117,	 269,117,	258,123,	250,130,	242,142,	200,142), 10, $color);
	imagefilledpolygon($image, array(319,107,	348,107,	364,142,	319,142), 4, $color);
	
	imagefilledpolygon($image, array(23,154,	132,154,	132,187,	11,187), 4, $color);
	imagefilledpolygon($image, array(147,154,	235,154,	223,187,	147,187), 4, $color);
	imagefilledpolygon($image, array(315,154,	369,154,	382,187,	325,187), 4, $color);
	
	
	if($width != 400)
	{
		
		$old_width = 400;
		$old_height = 200;
		
		// calculate thumbnail size
		//if($old_width > $old_height)
		{
			$new_width = $width;
			$new_height = floor( $old_height * ( $width / $old_width ) );
		}
		/*else 
		{
			$new_height = $width;
			$new_width = floor( $old_width * ( $width / $old_height ) );
		}*/
		
		// create a new temporary image
		$tmp_img = imagecreatetruecolor( $new_width, $new_height );

		// copy and resize old image into new image
		imagecopyresized( $tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height );
		$image = $tmp_img;
		
	}
	
	imagepng($image, $logo_dir . $logo_name);
	
}

header("Content-Type: image/png");
header('Content-disposition: inline; filename="' . $logo_name . '"'); 

header('Cache-control: max-age=' . (60*60*24*30));
header('Expires: ' . gmdate(DATE_RFC1123, time()+60*60*24*30));

readfile($logo_dir . $logo_name);

?>