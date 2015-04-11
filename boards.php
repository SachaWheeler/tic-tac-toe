<?php

// Create an image 

$width = 600;
$height = 800;
$line_width = 5;
$offset = ($width/3)/3;

$im = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$grey = imagecolorallocate($im, 0xbd, 0xbd, 0xbd);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$red = imagecolorallocate($im, 255, 0, 0);

// Set the background to be white
imagefilledrectangle($im, 0, 0, $width, $height, $white);

// Set the line thickness
imagesetthickness($im, $line_width);

// draw the grid
$grid = array(
		array(0, $width/3, $width, $width/3),
		array(0, ($width/3)*2, $width, ($width/3)*2),
		array($width/3, 0, $width/3, $width),
		array(($width/3)*2, 0, ($width/3)*2, $width),
	);
foreach($grid as $line)
	imageline ( $im , $line[0] , $line[1] , $line[2] , $line[3] , $black );

$board = str_split($_GET['board']);
if(isWinner($_GET['board'], 1))
	$state = "win";
elseif(isWinner($_GET['board'], 2))
	$state = "lose";
else
	$state = "play";
for($square=0 ; $square < count($board) ; $square++){
	list($x, $y) = getcoordinates($square);
	switch($board[$square]){
		case 0:
			if($state == "play") // draw the page number
				imagestring($im, 5, $x, $y, 'page #', $grey);
			break;
		case 1:
			// draw an 'x'
			imageline( $im , $x-$offset, $y-$offset, $x+$offset, $y+$offset, $grey);
			imageline( $im , $x-$offset, $y+$offset, $x+$offset, $y-$offset, $grey);
			break;
		case 2:
			// draw an 'o'
			// imageellipse ( $im , $x , $y , $offset*2 , $offset*2 , $grey );
			imagefilledellipse  ($im , $x , $y , ($offset+$line_width)*2 , ($offset+$line_width)*2 , $grey); 
			imagefilledellipse  ($im , $x , $y ,$offset*2 ,$offset*2 ,$white); 
			break;
	}
}
if($state != "play"){
	$coords = getwinningline($board);
	$start = getcoordinates($coords[0]);
	$end = getcoordinates($coords[1]);
	if($coords[2] == 'h'){
		$start[0] -= ($offset/2);
		$end[0]   += ($offset/2);
	}elseif($coords[2] == 'v'){
		$start[1] -= ($offset/2);
		$end[1]   += ($offset/2);
	}elseif($coords[2] == 'd1'){
		$start[0] -= ($offset/2);
		$start[1] -= ($offset/2);
		$end[0]   += ($offset/2);
		$end[1]   += ($offset/2);
	}elseif($coords[2] == 'd2'){
		$start[0] -= ($offset/2);
		$start[1] += ($offset/2);
		$end[0]   += ($offset/2);
		$end[1]   -= ($offset/2);
	}
	imageline( $im , $start[0], $start[1], $end[0], $end[1], $red);
}

// Output image to the browser
header('Content-Type: image/png');

imagepng($im);
imagedestroy($im);

function getwinningline($b){
                if($b[0] > 0 && $b[0] == $b[1] && $b[1] == $b[2] ) return array(0,2, 'h');
                if($b[3] > 0 && $b[3] == $b[4] && $b[4] == $b[5] ) return array(3,5, 'h');
                if($b[6] > 0 && $b[6] == $b[7] && $b[7] == $b[8] ) return array(6,8, 'h');

                if($b[0] > 0 && $b[0] == $b[3] && $b[3] == $b[6] ) return array(0,6, 'v');
                if($b[1] > 0 && $b[1] == $b[4] && $b[4] == $b[7] ) return array(1,7, 'v');
                if($b[2] > 0 && $b[2] == $b[5] && $b[5] == $b[8] ) return array(2,8, 'v');

                if($b[0] > 0 && $b[0] == $b[4] && $b[4] == $b[8] ) return array(0,8, 'd1');
                if($b[6] > 0 && $b[6] == $b[4] && $b[4] == $b[2] ) return array(6,2, 'd2');
}

function isWinner($board, $player){
        $b = str_split($board);
        if(
                ($b[0] == $player && $b[1] == $player && $b[2] == $player ) ||
                ($b[3] == $player && $b[4] == $player && $b[5] == $player ) ||
                ($b[6] == $player && $b[7] == $player && $b[8] == $player ) ||

                ($b[0] == $player && $b[3] == $player && $b[6] == $player ) ||
                ($b[1] == $player && $b[4] == $player && $b[7] == $player ) ||
                ($b[2] == $player && $b[5] == $player && $b[8] == $player ) ||

                ($b[0] == $player && $b[4] == $player && $b[8] == $player ) ||
                ($b[2] == $player && $b[4] == $player && $b[6] == $player ))
        return true;
}


function getcoordinates($cell){
	global $width, $height;
	if($cell <3) // top row
		$y = ($width/3) /2;
	elseif($cell < 6) // middle row
		$y = $width/2;
	else // bottom row
		$y = ($width/3) * 2.5;
	if($cell ==0 || $cell == 3 || $cell == 6) // left col
		$x = ($width/3) / 2;
	elseif($cell == 1 || $cell == 4 || $cell == 7) // middle col
		$x = $width/2;
	else // right col
		$x = ($width/3) * 2.5;

	return array($x, $y);
}

?>
