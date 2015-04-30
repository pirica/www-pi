<?php
$mysql_host = 'localhost';
$mysql_dbn = 'gps';
$mysql_user = 'root';
$mysql_pw = '';
require '/etc/mysql/conf.php';

$mysql_conn = mysql_connect($mysql_host, $mysql_user, $mysql_pw) ;//or die("Unable to connect to MySQL");
$mysql_db = mysql_select_db($mysql_dbn, $mysql_conn) ;//or die("Could not select examples");

?>