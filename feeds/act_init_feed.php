<?php
$error = 0;
$feed_title = '';
$feed_url = '';
$feed_refresh = 1;

		
while($feed = mysql_fetch_array($qry_feeds)){
	if($feed['id_feed'] == $id_feed){
		$feed_title = $feed['title'];
		$feed_url = $feed['url'];
		$feed_refresh = $feed['refresh'];
		
	}
}

?>