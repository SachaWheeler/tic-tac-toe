<?php
$im = imagecreatefrompng("img/tic-tac-toe-6.png");

header('Content-Type: image/png');

imagepng($im);
imagedestroy($im);

// header('Content-type: image/png');

// $image = new Imagick('img/tic-tac-toe-6.png');

// If 0 is provided as a width or height parameter,
// aspect ratio is maintained
// $image->thumbnailImage(100, 0);

// echo $image;

?>
