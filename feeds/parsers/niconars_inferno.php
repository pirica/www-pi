<?php

$str = file_get_contents($feeds['url']);

$doc = phpQuery::newDocumentHTML($str);

$title = $doc->find('.kadercomic img')->attr('alt');
$description = $doc->find('.kadercomic')->html();
$description = str_replace('src="', 'src="http://www.niconarsinferno.be/', $description);

?>