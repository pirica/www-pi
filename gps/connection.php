<?php
$mysql_host = 'localhost';
$mysql_dbn = 'gps';
$mysql_user = 'root';
$mysql_pw = '';

$mysql_conn = mysql_connect($mysql_host, $mysql_user, $mysql_pw) ;//or die("Unable to connect to MySQL");
$mysql_db = mysql_select_db($mysql_dbn, $mysql_conn) ;//or die("Could not select examples");

/*
//execute the SQL query and return records
$result = mysql_query("SELECT id, model, year FROM cars");
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
   echo "ID:".$row{'id'}." Name:".$row{'model'}."
   ".$row{'year'}."<br>";
}
*/
?>