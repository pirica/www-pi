<?php

$str = file_get_contents($feeds['url']);

//$str = file_get_contents('http://www.dustinland.com/');
//require dirname(__FILE__).'/../../_core/components/phpQuery/phpQuery.php';


$doc = phpQuery::newDocumentHTML($str);

foreach($doc['a'] as $a) {
	if(strpos($a->getAttribute('href'), 'archives/archives') !== false){
		$str = file_get_contents('http://www.dustinland.com/' . $a->getAttribute('href'));
		break;
	}
}

$doc = phpQuery::newDocumentHTML($str);

$title = $doc->find('title')->text();
$description = '<img src="http://www.dustinland.com/archives/' . $doc->find('img')->attr('src') . '">';

/*
echo $title;
echo "<br>";
echo $description
*/

?>