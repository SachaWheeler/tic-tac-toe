<?php

$start = "000000000";
$tree = array();

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

output($tree);
exit(0); 

getMoves($tree);
foreach($tree[$start] as $k => $x){
	if(is_array($x)){
		foreach($tree[$start][$k] as $k2 => $v){
			if($v == 'x')
				$tree[$start][$k][$k2] = addChildren($k2);
		}
	}
}

print_r($tree);

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
	$children = array();
	$done = array();
	$board_array = str_split($board);
	for($x=0 ; $x<count($board_array) ; $x++)
	{
		if(in_array($x, $done) || $board_array[$x] != '0') continue;
		$done[] = $x;
		$p1move = substr_replace($board, '1', $x, 1);

		if(isWinner($p1move, 1)){
			$children[$p2move] = "win";
			continue;
		}elseif(substr_count($p1move, '0') == 0){
			$children[$p1move] = "tie";
			continue;
		}

		$p2move = get_move($p1move);
		if(isWinner($p2move, 2)){
			$children[$p2move] = "lose";
			continue;
		}
		$children[$p2move] = 'x';
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
