<?php

$str = file_get_contents($feeds['url']);

$doc = phpQuery::newDocumentHTML($str);
$title = $doc->find('#ctitle')->text();
$description = $doc->find('#comic')->html();
$description .= '<br>' . $doc->find('#comic img')->attr('title');

?>