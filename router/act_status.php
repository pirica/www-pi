<?php

$data = [];

$app->setTitle('Status');

$range_start = time();
$range_end = $range_start - (7 * 24 * 60 * 60);

echo date("Y-m-d H:i:s", $range_start);
echo date("Y-m-d H:i:s", $range_end);


// http://css-tricks.com/snippets/css/a-guide-to-flexbox/
// http://bennettfeely.com/flexplorer/


include 'queries/pr_get_hosts.php';
include 'queries/pr_get_hosts_status.php';


?>