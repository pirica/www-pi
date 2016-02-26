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

$thumbWidth = 180; // setting

if(
	file_exists($settings->val('photos_path', '') . $src)
	&&
	(
		!file_exists($settings->val('thumbs_path', '') . $src)
		||
		(/*thumb date modified*/ 1 < /*photo date modified*/ 0)
	)
)
{
	if(stripos($src, '.jpg') == 'jpg')
	{
		// load image and get image size
		$img = imagecreatefromjpeg($settings->val('photos_path', '') . $src);
		$width = imagesx( $img );
		$height = imagesy( $img );

		// calculate thumbnail size
		$new_width = $thumbWidth;
		$new_height = floor( $height * ( $thumbWidth / $width ) );

		// create a new temporary image
		$tmp_img = imagecreatetruecolor( $new_width, $new_height );

		// copy and resize old image into new image
		imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

		// save thumbnail into a file
		imagejpeg( $tmp_img, $settings->val('thumbs_path', '') . $src);
	}
}

readfile($settings->val('thumbs_path', '') . $src);

?>