<?php

$enable_nma = 1;
$enable_ortc = 0;
$messages_host = 'nasberrypi';

$mysql_host = 'localhost';
$mysql_dbn = 'router';
$mysql_dbn_users = 'router';
$mysql_user = 'root';
$mysql_pw = '';
require '/etc/mysql/conf.php';

$mysql_failed_inserts = "/var/www/messagelogs/".$mysql_dbn . "_" . date('Ymd', time()) . ".sql";

$conn = @mysqli_connect($mysql_host, $mysql_user, $mysql_pw, $mysql_dbn) ;//or die("Unable to connect to MySQL");

?>