<?php
require '../_core/webinit.php';

require 'connections.php';
//require '../_core/functions.php';

require dirname(__FILE__).'/../_core/components/images/GIFEncoder.class.php';

// test: https://wikke.duckdns.org/photos/thumb_movie.php?src=/Marie/20130716_212425.mp4


$src = saneInput('src', 'string', '');
$fa = explode('/',$src);
$filename = array_pop($fa);

ob_clean();

header('Content-disposition: inline; filename="' . $filename . '".gif'); 

header('Cache-control: max-age='.(60*60*24*30));
header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*30));

$thumbWidth = 180; // setting
$square = 0;//$settings->val('create_square_thumbs', 0);

$thumbnail = $settings->val('thumbs_path', '') . $thumbWidth . ($square == 1 ? 'square' : 'prop') . '/' . $src . '.gif';

if(
	file_exists($settings->val('photos_path', '') . $src)
	&&
	(
		!file_exists($thumbnail)
		||
		(filemtime($thumbnail) < filemtime($settings->val('photos_path', '') . $src))
	)
)
{
	
	// create directory if not exists
	$parts = explode('/', $settings->val('thumbs_path', '') . $thumbWidth, -1);
	$dir = '';
	foreach($parts as $part){
		if(!is_dir($dir .= "/$part")) mkdir($dir);
	}
	if(!is_dir("/dev/shm/palette")) mkdir("/dev/shm/palette");
	
	if(stripos($src, '.mp4') > 0)
	{
		
		// create directory if not exists
		$parts = explode('/', $thumbnail, -1);
		$dir = '';
		$filename = '';
		foreach($parts as $part){
			$filename = $part;
			if(!is_dir($dir .= "/$part")) mkdir($dir);
		}
		
		shell_exec('ffmpeg -y -i ' . $settings->val('photos_path', '') . $src . ' -vf fps=1,scale='.$thumbWidth.':-1:flags=lanczos,palettegen /dev/shm/palette/pallete_'.$filename.'.png');
		shell_exec('ffmpeg -i ' . $settings->val('photos_path', '') . $src . ' -i /dev/shm/palette/pallete_'.$filename.'.png'.' -filter_complex "fps=1,scale='.$thumbWidth.':-1:flags=lanczos[x];[x][1:v]paletteuse" ' . $thumbnail);
		
	}
}

header("Content-Type: image/gif");
readfile($thumbnail);

?>