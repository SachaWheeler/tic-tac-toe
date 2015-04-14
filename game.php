<?php

$start = "000000000";
$tree = array();
$pages = array($start);
$page_pointers = array();

// presentation vars
$width = 1800;
$height = 2200;
$line_width = 25;
$offset = ($width/3)/3;
	
$font_path = "/Library/Fonts/Courier New Bold.ttf";
$page_font_size = 40;
$result_size = 80;
$image_count = 0;
	
// first and second moves
$tree[$start] = addChildren($start);

// third and fourth moves
foreach($tree[$start] as $k => $v){
	if($v == 'x')
		$tree[$start][$k] = addChildren($k);
}

// fifth and sixth moves
foreach($tree[$start] as $k => $v){
	foreach($v as $k2 => $v2){
		if($v2 == 'x')
			$tree[$start][$k][$k2] = addChildren($k2);
	}
}

// seventh and eigth moves
foreach($tree[$start] as $k => $v){
	foreach($v as $k2 => $v2){
		foreach($v2 as $k3 => $v3){
			if($v3 == 'x')
				$tree[$start][$k][$k2][$k3] = addChildren($k3);
		}
	}
}

foreach($tree[$start] as $k => $v){
	foreach($v as $k2 => $v2){
		foreach($v2 as $k3 => $v3){
			if(is_array($v3)){
				foreach($v3 as $k4 => $v4){
					if($v4 == 'x')
						$tree[$start][$k][$k2][$k3][$k4] = addChildren($k4);
				}
			}
		}
	}
}

foreach($tree[$start] as $k => $v){
	foreach($v as $k2 => $v2){
		foreach($v2 as $k3 => $v3){
			if(is_array($v3)){
				foreach($v3 as $k4 => $v4){
					if(is_array($v4)){
						foreach($v4 as $k5 => $v5){
							if($v5 == 'x')
								$tree[$start][$k][$k2][$k3][$k4][$k5] = addChildren($k5);
						}
					}
				}
			}
		}
	}
}

//output($tree);
//output($pages);
//output($page_pointers);
foreach($pages as $page){
	save_image($page);
}
exit(0); 


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

function get_move($board){
	// echo "board: {$board}\n";

	$b = str_split($board);

	// try to win
	if(	$b[0] == 0 &&	(($b[1] == 2 && $b[2] == 2) ||
				 ($b[3] == 2 && $b[6] == 2) ||
				 ($b[4] == 2 && $b[8] == 2)))
		$move = 0;
	elseif(	$b[1] == 0 &&	(($b[0] == 2 && $b[2] == 2) ||
				 ($b[4] == 2 && $b[7] == 2)))
		$move = 1;
	elseif(	$b[2] == 0 &&	(($b[0] == 2 && $b[1] == 2) ||
				 ($b[4] == 2 && $b[6] == 2) ||
				 ($b[5] == 2 && $b[8] == 2)))
		$move = 2;
	elseif(	$b[3] == 0 &&	(($b[0] == 2 && $b[6] == 2) ||
				 ($b[4] == 2 && $b[5] == 2)))
		$move = 3;
	elseif(	$b[4] == 0 &&	(($b[0] == 2 && $b[8] == 2) ||
				 ($b[2] == 2 && $b[6] == 2) ||
				 ($b[1] == 2 && $b[7] == 2) ||
				 ($b[3] == 2 && $b[5] == 2)))
		$move = 4;
	elseif(	$b[5] == 0 &&	(($b[2] == 2 && $b[8] == 2) ||
				 ($b[3] == 2 && $b[4] == 2)))
		$move = 5;
	elseif(	$b[6] == 0 &&	(($b[0] == 2 && $b[3] == 2) ||
				 ($b[4] == 2 && $b[2] == 2) ||
				 ($b[7] == 2 && $b[8] == 2)))
		$move = 6;
	elseif(	$b[7] == 0 &&	(($b[1] == 2 && $b[4] == 2) ||
				 ($b[6] == 2 && $b[8] == 2)))
		$move = 7;
	elseif(	$b[8] == 0 &&	(($b[0] == 2 && $b[4] == 2) ||
				 ($b[6] == 2 && $b[7] == 2) ||
				 ($b[2] == 2 && $b[5] == 2)))
		$move = 8;

	// try to block them winning
	elseif(	$b[0] == 0 &&	(($b[1] == 1 && $b[2] == 1) ||
				 ($b[3] == 1 && $b[6] == 1) ||
				 ($b[4] == 1 && $b[8] == 1)))
		$move = 0;
	elseif(	$b[1] == 0 &&	(($b[0] == 1 && $b[2] == 1) ||
				 ($b[4] == 1 && $b[7] == 1)))
		$move = 1;
	elseif(	$b[2] == 0 &&	(($b[0] == 1 && $b[1] == 1) ||
				 ($b[4] == 1 && $b[6] == 1) ||
				 ($b[5] == 1 && $b[8] == 1)))
		$move = 2;
	elseif(	$b[3] == 0 &&	(($b[0] == 1 && $b[6] == 1) ||
				 ($b[4] == 1 && $b[5] == 1)))
		$move = 3;
	elseif(	$b[4] == 0 &&	(($b[0] == 1 && $b[8] == 1) ||
				 ($b[2] == 1 && $b[6] == 1) ||
				 ($b[1] == 1 && $b[7] == 1) ||
				 ($b[3] == 1 && $b[5] == 1)))
		$move = 4;
	elseif(	$b[5] == 0 &&	(($b[2] == 1 && $b[8] == 1) ||
				 ($b[3] == 1 && $b[4] == 1)))
		$move = 5;
	elseif(	$b[6] == 0 &&	(($b[0] == 1 && $b[3] == 1) ||
				 ($b[4] == 1 && $b[2] == 1) ||
				 ($b[7] == 1 && $b[8] == 1)))
		$move = 6;
	elseif(	$b[7] == 0 &&	(($b[1] == 1 && $b[4] == 1) ||
				 ($b[6] == 1 && $b[8] == 1)))
		$move = 7;
	elseif(	$b[8] == 0 &&	(($b[0] == 1 && $b[4] == 1) ||
				 ($b[6] == 1 && $b[7] == 1) ||
				 ($b[2] == 1 && $b[5] == 1)))
		$move = 8;
	
	// try the centre square
	elseif( $b[4] == 0)
		$move = 4;

	// otherwise, random
	else
		$move = strpos($board, '0');

	if(isset($move))
		return substr_replace($board, '2', $move, 1);
	else
		die("no move: ".$board);
}

function addChildren($board){
	global $pages, $page_pointers;
	$children = array();
	$done = array();
	$board_array = str_split($board);
	for($x=0 ; $x<count($board_array) ; $x++)
	{
		if(in_array($x, $done) || $board_array[$x] != '0') continue;
		$done[] = $x;

		$p1move = substr_replace($board, '1', $x, 1);
		if(isWinner($p1move, 1)){
			$children[$p1move] = "win";
			if(!in_array($p1move, $pages)) $pages[] = $p1move;
			continue;
		}
		if(substr_count($p1move, '0') == 0){
			$children[$p1move] = "tie";
			if(!in_array($p1move, $pages)) $pages[] = $p1move;
			continue;
		}

		$p2move = get_move($p1move);
		if(isWinner($p2move, 2)){
			$children[$p2move] = "lose";
			if(!in_array($p2move, $pages)) $pages[] = $p2move;
			$page_pointers[$p1move] = array_search($p2move, $pages);
			continue;
		}
		$children[$p2move] = 'x';
		if(!in_array($p2move, $pages)) $pages[] = $p2move;

		$page_pointers[$p1move] = array_search($p2move, $pages);

		// $page_pointers[$board][$x] = key( array_slice( $pages, -1, 1, TRUE ) );
	}
	return $children;
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

function output($var){
	print_r($var);
}

?>
