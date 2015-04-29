<?php
set_time_limit(0);

include 'queries/pr_get_share_stats.php';

$returnvalue = array('data' => mysql2json($qry_share_stats));

?>