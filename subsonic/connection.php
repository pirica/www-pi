<?php
$mysql_host = 'localhost';
$mysql_dbn = 'subsonic';
$mysql_dbn_users = 'users';
$mysql_user = 'root';
$mysql_pw = '';
require '/etc/mysql/conf.php';

$conn = mysql_connect($mysql_host, $mysql_user, $mysql_pw) ;//or die("Unable to connect to MySQL");
mysql_select_db($mysql_dbn, $conn) ;//or die("Could not select examples");

?>