<?php
set_time_limit(0);

require dirname(__FILE__).'/../_core/appinit.php';

require 'connection.php';
require 'functions.php';

require dirname(__FILE__).'/../_core/components/images/GIFEncoder.class.php';

$qry_camera_log = mysql_query("
		
		select
			cl.id_camera_log,
			cl.date,
			cl.time,
			cl.hour_lbl,
			cl.time_value,
			cl.time_value_gif,
			cl.name
			
		from t_camera_log cl
		where
			status = 0
			and cl.name like '%.jpg'
		
		order by
			cl.date,
			cl.time_value_gif,
			cl.name
			
			
		");

$prev_time_value_gif = -9999;

$frames = array();
$framed = array();

$thumbWidth = 320;

$gifname = '';

while($camera_log = mysql_fetch_array($qry_camera_log)){
	$extarr = explode('.', $camera_log['name']);
	$extension = '.' . $extarr[count($extarr) - 1];
	
	if($gifname == ''){
		$gifname = $main_dir . $camera_log['date'] . '/' . $camera_log['date'] . '_' . str_replace(':', '', $camera_log['time']) . '.gif';
	}
	
	if(strtolower($extension) == '.jpg' && exif_imagetype($main_dir . $camera_log['date'] . '/' . $camera_log['name']) == IMAGETYPE_JPEG){
		if(!file_exists($gifname)){
			if($prev_time_value_gif != $camera_log['time_value_gif']){
				$prev_time_value_gif = $camera_log['time_value_gif'];
				
				if(count($framed) > 0){
					// Generate the animated gif and output to screen.
					$gif = new GIFEncoder($frames, $framed, 0, 2, 0, 0, 0, 'bin');

					$fp = fopen($gifname, 'w');
					fwrite($fp, $gif->GetAnimation());
					fclose($fp);

					$frames = array();
					$framed = array();
					
					$gifname = $main_dir . $camera_log['date'] . '/' . $camera_log['date'] . '_' . str_replace(':', '', $camera_log['time']) . '.gif';
					
				}
			}
			
			// Open the first source image and add the text.
			$image = imagecreatefromjpeg($main_dir . $camera_log['date'] . '/' . $camera_log['name']);
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
}

if(count($framed) > 0){
	// Generate the animated gif and output to screen.
	$gif = new GIFEncoder($frames, $framed, 0, 2, 0, 0, 0, 'bin');

	$fp = fopen($gifname);
	fwrite($fp, $gif->GetAnimation());
	fclose($fp);

}

?>