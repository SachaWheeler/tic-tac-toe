<?php

$width = 2400;
$height = 3200;
$line_width = 20;
$offset = ($width/3)/3;
	
$font_path = "/Library/Fonts/Courier New Bold.ttf";
$page_font_size = 40;
$result_size = 70;
$image_count = 0;
	
function save_image($board_string){
	// Create an image 
	global $width, $height, $line_width, $offset, $font_path, $page_font_size, $result_size, $pages;
	global $page_pointers, $image_count;
	$image_count++;

	$im = imagecreatetruecolor($width, $height);
	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
	$grey = imagecolorallocate($im, 0xbd, 0xbd, 0xbd);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	$red = imagecolorallocate($im, 255, 0, 0);
	
	imagecolortransparent($im, $white);
	
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
	
	$board = str_split($board_string);
	if(isWinner($board_string, 1))
		$state = "win";
	elseif(isWinner($board_string, 2))
		$state = "lose";
	elseif(substr_count($board_string, 0) == 0)
		$state = "tie";
	else
		$state = "play";
	for($square=0 ; $square < count($board) ; $square++){
		list($x, $y) = getcoordinates($square);
		switch($board[$square]){
			case 0:
				if($state == "play"){ // draw the page number
					// get the page number
					$dest_page_board = substr_replace($board_string, '1', $square, 1);

					// print_r($board_string); echo "\n"; print_r($dest_page_board); die(0);
					if(in_array($dest_page_board, $pages))
						$text = array_search($dest_page_board, $pages)+1;
					elseif(isset($page_pointers[$dest_page_board]))
						$text = $page_pointers[$dest_page_board]+1;
					else
						$text = "page #";
					$bb = imagettfbbox ( $page_font_size , 0 , $font_path , $text );
					$half_text_width = ($bb[2] - $bb[0]) /2;
					$half_text_height = ($bb[7] - $bb[1]) /2;
					imagettftext($im, $page_font_size, 0, $x - $half_text_width, $y - $half_text_height, $black, $font_path, $text);
				}
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
	if($state == "tie"){
		// tie
		$state = "it's a tie";
		$bb = imagettfbbox ( $result_size , 0 , $font_path , $state );
		$half_text_width = ($bb[2] - $bb[0]) /2;
		imagettftext($im, $result_size, 0, $width/2 - $half_text_width, $width+($height-$width)/2, $red, $font_path, $state);
	}elseif($state != "play"){
		$state = "you ".$state;
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
		imagesetthickness($im, $line_width*2);
		imageline( $im , $start[0], $start[1], $end[0], $end[1], $red);
	
		$bb = imagettfbbox ( $result_size , 0 , $font_path , $state );
		$half_text_width = ($bb[2] - $bb[0]) /2;
		imagettftext($im, $result_size, 0, $width/2 - $half_text_width, $width+($height-$width)/2, $red, $font_path, $state);
	}
	
	// Output image to the browser
	//header('Content-Type: image/png');
	
	imagepng($im, "./results/".sprintf("%03d", $image_count)."-".$board_string.".png");
	//imagedestroy($im);
}
	
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
