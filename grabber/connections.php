<?php
$mysql_host = 'localhost';
$mysql_dbn = 'grabber';
$mysql_dbn_users = 'users';
$mysql_user = 'root';
$mysql_pw = '';

$conn = mysql_connect($mysql_host, $mysql_user, $mysql_pw) or die("Unable to connect to db");
//$connusers = mysql_connect($mysql_host, $mysql_user, $mysql_pw) ;
//$mysql_db = 
mysql_select_db($mysql_dbn, $conn) or die("Could not select db");
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