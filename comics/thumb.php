<?php
require '../_core/webinit.php';

require 'connections.php';
require 'functions.php';

$src = saneInput('src', 'string', '');
$comics = array();

$fulldir = $settings->val('comics_path', '') . $src;
if(is_dir($fulldir))
{
	list_dir($comics, $fulldir, 0, 1, 0);
	usort($comics, "arraysort_compare");
}

$file = $src . '/' . $comics[0]['name'];
$filename = $comics[0]['name'];

ob_clean();

header('Cache-control: max-age='.(60*60*24*30));
header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*30));

$thumbWidth = 240; // setting

if(stripos($comics[0]['name'], '.jpg') > 0 || stripos($comics[0]['name'], '.jpeg') > 0)
{
	$thumbnail = $settings->val('thumbs_path', '') . $thumbWidth . '/' . $src . '.jpg';
	header('Content-disposition: inline; filename="' . $src . '.jpg' . '"'); 
}
else if(stripos($comics[0]['name'], '.png') > 0)
{
	$thumbnail = $settings->val('thumbs_path', '') . $thumbWidth . '/' . $src . '.png';
	header('Content-disposition: inline; filename="' . $src . '.png' . '"'); 
}

if(
	file_exists($settings->val('comics_path', '') . $file)
	&&
	(
		!file_exists($thumbnail)
		||
		(filemtime($thumbnail) < filemtime($settings->val('comics_path', '') . $file))
	)
)
{
	
	// create directory if not exists
	$parts = explode('/', $thumbnail, -1);
	$dir = '';
	foreach($parts as $part){
		if(!is_dir($dir .= "/$part")) mkdir($dir);
	}
	
	if(stripos($file, '.jpg') > 0 || stripos($file, '.jpeg') > 0 || stripos($file, '.png') > 0)
	{
		
		// create directory if not exists
		$parts = explode('/', $thumbnail, -1);
		$dir = '';
		foreach($parts as $part){
			if(!is_dir($dir .= "/$part")) mkdir($dir);
		}
		
		// load image and get image size
		if(stripos($file, '.jpg') > 0 || stripos($file, '.jpeg') > 0)
		{
			$img = imagecreatefromjpeg($settings->val('comics_path', '') . $file);
		}
		else if(stripos($file, '.png') > 0)
		{
			$img = imagecreatefrompng($settings->val('comics_path', '') . $file);
		}
		
		$width = imagesx( $img );
		$height = imagesy( $img );
		$src_x = 0;
		$src_y = 0;
		
		// calculate thumbnail size
		if($width > $height)
		{
			$new_width = $thumbWidth;
			$new_height = floor( $height * ( $thumbWidth / $width ) );
		}
		else 
		{
			$new_height = $thumbWidth;
			$new_width = floor( $width * ( $thumbWidth / $height ) );
		}
		
		// create a new temporary image
		$tmp_img = imagecreatetruecolor( $new_width, $new_height );

		// copy and resize old image into new image
		imagecopyresized( $tmp_img, $img, 0, 0, $src_x, $src_y, $new_width, $new_height, $width, $height );
		
		
		if(stripos($file, '.jpg') > 0 || stripos($file, '.jpeg') > 0)
		{
			// save thumbnail into a file
			imagejpeg( $tmp_img, $thumbnail);
			
			header("Content-Type: image/jpeg");
			
		}
		else if(stripos($file, '.png') > 0)
		{
			// save thumbnail into a file
			imagepng( $tmp_img, $thumbnail);
			
			header("Content-Type: image/png");
			
		}
		
	}
}

readfile($thumbnail);

?>