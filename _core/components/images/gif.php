<?php
include('GIFEncoder.class.php');

// save as file
$output_file = false;

if(!$output_file) header('Content-type:image/gif');

$text = "Hello World";

// Open the first source image and add the text.
$image = imagecreatefrompng('source01.png');
$text_color = imagecolorallocate($image, 200, 200, 200);
imagestring($image, 5, 5, 5,  $text, $text_color);

// Generate GIF from the $image
// We want to put the binary GIF data into an array to be used later,
//  so we use the output buffer.
ob_start();
imagegif($image);
$frames[]=ob_get_contents();
$framed[]=40; // Delay in the animation.
ob_end_clean();

// And again..

// Open the first source image and add the text.
$image = imagecreatefrompng('source02.png');
$text_color = imagecolorallocate($image, 200, 200, 200);
imagestring($image, 5, 20, 20,  $text, $text_color);

// Generate GIF from the $image
// We want to put the binary GIF data into an array to be used later,
//  so we use the output buffer.
ob_start();
imagegif($image);
$frames[]=ob_get_contents();
$framed[]=40; // Delay in the animation.
ob_end_clean();

// Generate the animated gif and output to screen.
$gif = new GIFEncoder($frames,$framed,0,2,0,0,0,'bin');

if($output_file){
	$fp = fopen('animegif.gif', 'w');
	fwrite($fp, $gif->GetAnimation());
	fclose($fp);
}
else {
	echo $gif->GetAnimation();
}

?>