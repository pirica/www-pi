<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

require 'connection.php';
require '../_core/functions.php';
require dirname(__FILE__).'/../_core/components/images/GIFEncoder.class.php';

$date = saneInput('date', 'string', '');
$time = saneInput('time', 'string', '');
$archived = 0;

header("Content-Type: image/gif");
header('Content-disposition: inline; filename="' . $date . '_' . $time . '.gif"'); 

$thumbWidth = 320;

if($date != ''){
	if(){
		
		include 'queries/pr_get_camera_log'.($archived == 1 ? '_archived' : '').'.php';
		
		while($camera_log = mysql_fetch_array($qry_camera_log)){
			$extarr = explode('.', $camera_log['name']);
			$extension = '.' . $extarr[count($extarr) - 1];
			
			if(strtolower($extension) == '.jpg'){
				// Open the first source image and add the text.
				$image = imagecreatefromjpeg($main_dir . $date . '/' . $camera_log['name']);
				//$text_color = imagecolorallocate($image, 200, 200, 200);
				//imagestring($image, 5, 5, 5,  $text, $text_color);

				$width = imagesx( $image );
				$height = imagesy( $image );

				// calculate thumbnail size
				$new_width = $thumbWidth;
				$new_height = floor( $height * ( $thumbWidth / $width ) );

				// create a new temporary image
				$tmp_image = imagecreatetruecolor( $new_width, $new_height );

				// copy and resize old image into new image
				imagecopyresized( $tmp_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

				ob_start();
				imagegif($tmp_image);
				$frames[] = ob_get_contents();
				$framed[] = 50; // Delay in the animation. (1/100)
				ob_end_clean();
			}
		}
		
		// Generate the animated gif and output to screen.
		$gif = new GIFEncoder($frames, $framed, 0, 2, 0, 0, 0, 'bin');
		
		$fp = fopen($main_dir . $date . '/' . $date . '_' . $time . '.gif', 'w');
		fwrite($fp, $gif->GetAnimation());
		fclose($fp);
		
		//echo $gif->GetAnimation();
		
	}
	
	include $main_dir . $date . '/' . $date . '_' . $time . '.gif';
}

?>