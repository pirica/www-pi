<?php


//parse_str(implode('&', array_slice($argv, 1)), $_GET);

/*
$argv = [
	0: script name (/var/www/router/html.php)
	1: action (l33t, uppercase, xkcdify)
	2: file name (/var/www/squid/cache/2547-1) (pid-count)
]
*/

$action = $argv[1];
$html = file_get_contents($argv[2]);

$in_title = 0;

/*
$english = array("a", "e", "s", "S", "A", "o", "O", "t", "l", "ph", "y", "H", "W", "M", "D", "V", "x"); 
$leet = array("4", "3", "z", "Z", "4", "0", "0", "+", "1", "f", "j", "|-|", "\\/\\/", "|\\/|", "|)", "\\/", "><");
function leet_encode($string)
{
	$result = '';
	for ($i = 0; $i < strlen($string); $i++) 
	{
		$char = $string[$i];

		if (false !== ($pos = array_search($char, $this->english))) 
		{
			$char = $this->leet[$pos]; //Change the char to l33t.
		}
		$result .= $char;
	}
	return $result; 
} 
*/

// separate the tags from the text.
$pieces =
	preg_split(
		'/(<.+?>)/',
		$html,
		-1,
		PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
	);

foreach ($pieces as &$piece) {
	// make the substitution on the non-tag pieces.
	if (strpos($piece, '<') === FALSE) {
		//echo $piece, '<br>';
		switch($action){
			case 'l33t':
				if(stripos('the ', $piece) !== false){
					if(rand(0,10) > 3) $piece = str_replace(' the ', ' teh ', $piece);
					if(rand(0,10) > 3) $piece = str_replace('The ', 'Teh ', $piece);
				}
				else {
					if(rand(0,10) > 3) $piece = str_replace('e', '3', $piece);
					if(rand(0,10) > 3) $piece = str_replace('E', '3', $piece);
					if(rand(0,10) > 3) $piece = str_replace('h', '|-|', $piece);
					if(rand(0,10) > 3) $piece = str_replace('H', '|-|', $piece);
					if(rand(0,10) > 3) $piece = str_replace('t', '7', $piece);
					if(rand(0,10) > 3) $piece = str_replace('T', '7', $piece);
				}
				if(rand(0,10) > 3) $piece = str_replace('a', '4', $piece);
				if(rand(0,10) > 3) $piece = str_replace('A', '4', $piece);
				if(rand(0,10) > 3) $piece = str_replace('b', '8', $piece);
				if(rand(0,10) > 3) $piece = str_replace('B', '8', $piece);
				if(rand(0,10) > 3) $piece = str_replace('o', '0', $piece);
				if(rand(0,10) > 3) $piece = str_replace('O', '0', $piece);
				if(rand(0,10) > 3) $piece = str_replace('v', '\/', $piece);
				if(rand(0,10) > 3) $piece = str_replace('V', '\/', $piece);
				if(rand(0,10) > 3) $piece = str_replace('l', '|', $piece);
				if(rand(0,10) > 3) $piece = str_replace('L', '|_', $piece);
				if(rand(0,10) > 3) $piece = str_replace('s ', 'z ', $piece);
				if(rand(0,10) > 3) $piece = str_replace('S ', 'Z ', $piece);
				if(rand(0,10) > 3) $piece = str_replace('m', '|\/|', $piece);
				if(rand(0,10) > 3) $piece = str_replace('M', '|\/|', $piece);
				if(rand(0,10) > 3) $piece = str_replace('n', '|\|', $piece);
				if(rand(0,10) > 3) $piece = str_replace('N', '|\|', $piece);
				if(rand(0,10) > 3) $piece = str_replace('w', '\/\/', $piece);
				if(rand(0,10) > 3) $piece = str_replace('W', '\/\/', $piece);
				
				$exclrand = rand(0,10);
				if($exclrand < 3) $piece = str_replace('!', '!!!1!', $piece);
				else if($exclrand >= 3 && $exclrand < 8) $piece = str_replace('!', '!!!!11!', $piece);
				else $piece = str_replace('!', '!!!!!111!!', $piece);
				
				if($in_title == 1){
					$piece = 'Haxxored!!!!11!!' . $piece;
				}
				
				break;
			case 'uppercase':
				$piece = strtoupper($piece);
				break;
		}
	}
	// and ignore the tag pieces.
	else {
		//echo 'Skipping ', htmlentities($piece), '<br>';
		if($piece == '<title>'){
			$in_title = 1;
		}
		else {
			$in_title = 0;
		}
		
	}
}
// Now put them back together...
$htmlout = implode('', $pieces);
// and save
file_get_contents($argv[2], $htmlout);

/*
$tests = array(
	'<a href="mailto:Bob@Bob.com">E-mail</a> Bob.',
	'<a href="mailto:Bob@Bob.com">Bob</a>',
	'Bob',
	'<a href="mailto:Sue@Sue.com">Sue</a>',
);

foreach ($tests as $test) {
	echo '<hr /><b>Analyzing: ', htmlentities($test), '</b><br />';
	// separate the tags from the text.
	$pieces =
		preg_split(
			'/(<.+?>)/',
			$test,
			-1,
			PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
		);
	
	foreach ($pieces as &$piece) {
		// make the substitution on the non-tag pieces.
		if (strpos($piece, '<') === FALSE) {
			echo $piece, '<br>';
			$piece = str_replace('Bob', 'Bill', $piece);
		}
		// and ignore the tag pieces.
		else {
			echo 'Skipping ', htmlentities($piece), '<br>';
		}
	}
	// Now put them back together...
	$string = implode('', $pieces);
	echo '<b>Final: ', htmlentities($string), '</b><br>';
}
*/

?>
