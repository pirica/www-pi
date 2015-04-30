<?php
$mysql_host = 'localhost';
$mysql_dbn = 'grabber';
$mysql_dbn_users = 'users';
$mysql_user = 'root';
$mysql_pw = '';
require '/etc/mysql/conf.php';

$conn = mysql_connect($mysql_host, $mysql_user, $mysql_pw) or die("Unable to connect to db");
mysql_select_db($mysql_dbn, $conn) or die("Could not select db");

?>