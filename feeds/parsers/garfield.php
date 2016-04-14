<?php

$str = file_get_contents($feeds['url']);

//$str = file_get_contents('http://garfield.com/');
//require dirname(__FILE__).'/../../_core/components/phpQuery/phpQuery.php';


$doc = phpQuery::newDocumentHTML($str);
$title = $doc->find('.comic-display img')->attr('src');
$title = explode('/', $title);
$title = $title[count($title) - 1];
$title = explode('.', $title)[0];
$description = '<img src="' . $doc->find('.comic-display img')->attr('src') . '" alt="">';


/*
echo $title;
echo "<br>";
echo $description
// */
?>