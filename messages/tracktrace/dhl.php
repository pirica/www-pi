<?php

$str = file_get_contents('https://dhlparcel.nl/nl/particulier/ontvangen/track-trace?tt=' . $tt['tracking_code']);
//echo $str;
//echo "\n\n==================\n\n";


$msg = explode('<td class="definition">', $str, 2)[1];
$msg = explode('</td>', $msg)[0];
$msg = str_replace('<br>', ' ', $msg);
$msg = str_replace('<br/>', ' ', $msg);
$msg = str_replace('<br />', ' ', $msg);
$msg = strip_tags($msg);

?>