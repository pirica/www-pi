<?php
$error = 0;
$feed_title = '';
$feed_url = '';
$feed_refresh = '';
$feed_parser = '';
$feed_parse_max_items = '';

while($feed = mysqli_fetch_array($qry_feeds)){
	if($feed['id_feed'] == $id_feed){
		$feed_title = $feed['title'];
		$feed_url = $feed['url'];
		$feed_refresh = $feed['refresh'];
		$feed_parser = $feed['parser'];
		$feed_parse_max_items = $feed['parse_max_items'];
		
	}
}

?>