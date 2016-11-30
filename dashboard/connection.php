<?php
$mysql_host = 'localhost';
$mysql_dbn = 'dashboard';
$mysql_dbn_users = 'users';
$mysql_user = 'root';
$mysql_pw = '';
require '/etc/mysql/conf.php';

$conn = mysqli_connect($mysql_host, $mysql_user, $mysql_pw, $mysql_dbn) ;//or die("Unable to connect to MySQL");

?>