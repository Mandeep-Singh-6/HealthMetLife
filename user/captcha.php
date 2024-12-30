<?php
session_start();

// If increasing or decreasing the value, take care of the dimensions of the image.
$captcha_length = 6;
$generated_captcha = substr(md5(rand()), 0, $captcha_length);

// Storing the generated captcha in a session.
$_SESSION['captcha'] = $generated_captcha;

// Setting the height and width of image in px.
$image_height = 30;
$image_width = 100;

// Creating a true colour image.
$image = imagecreatetruecolor($image_width, $image_height);

// Setting the colours.
// white background.
$background_color = imagecolorallocate($image, 255, 255, 255);
// black font.
$text_color = imagecolorallocate($image, 0, 0, 0);

// Filling the background color.
// Adding 20px to dimensions just to be on the safe side. The image dimensions
// will still remain the same.
imagefilledrectangle($image, 0, 0, $image_width + 20, $image_height + 20, $background_color);

// Adding the text in the image.
imagestring($image, 5, 30, 10, $generated_captcha, $text_color);

// Output the image 
header('Content-Type: image/png'); 
imagepng($image); 

// Freeing memory.
imagedestroy($image);
?>