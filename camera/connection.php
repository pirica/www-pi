<?php
$mysql_host = 'localhost';
$mysql_dbn = 'router';
$mysql_dbn_users = 'users';
$mysql_user = 'root';
$mysql_pw = '';
require '/etc/mysql/conf.php';

$main_dir = '/var/docs/motion/';
$archive_dir = '/var/docs/motion_archive/';
$thumbs_dir = '/var/docs/motion_thumbs/';

$conn = mysqli_connect($mysql_host, $mysql_user, $mysql_pw, $mysql_dbn) ;//or die("Unable to connect to MySQL");

?>