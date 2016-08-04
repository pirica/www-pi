<?php

$str = file_get_contents('http://www.smbc-comics.com/');

if (!isset($feeds)){
	require dirname(__FILE__).'/../../_core/components/phpQuery/phpQuery.php';
}

$doc = phpQuery::newDocumentHTML($str);

$title = $doc->find('#cc-comicbody img')->attr('title');
$description = '<img src="' . $doc->find('#cc-comicbody img')->attr('src') . '"><br>';

$description .= '<img src="' . $doc->find('#aftercomic img')->attr('src') . '"><br>';



if (!isset($feeds)){
	echo $title;
	echo "<br>";
	echo $description;
}

?>