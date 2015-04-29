<?php

$enable_nma = 1;
$enable_ortc = 0;
$messages_host = 'nasberrypi';

$mysql_host = 'localhost';
$mysql_dbn = 'router';
$mysql_dbn_users = 'router';
$mysql_user = 'root';
$mysql_pw = '';

//$mysql_failed_inserts = "/media/usbdrive2/mysql/".$mysql_dbn . "_" . date('Ymd', time()) . ".sql";
$mysql_failed_inserts = "/var/www/messagelogs/".$mysql_dbn . "_" . date('Ymd', time()) . ".sql";

$conn = @mysql_connect($mysql_host, $mysql_user, $mysql_pw) ;//or die("Unable to connect to MySQL");
//$connusers = mysql_connect($mysql_host, $mysql_user, $mysql_pw) ;
@mysql_select_db($mysql_dbn, $conn) ;//or die("Could not select examples");
//mysql_select_db($mysql_dbn_users, $connusers) ;

/*
//execute the SQL query and return records
$result = mysql_query("SELECT id, model, year FROM cars", $conn);
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
   echo "ID:".$row{'id'}." Name:".$row{'model'}."
   ".$row{'year'}."<br>";
}
*/

?>