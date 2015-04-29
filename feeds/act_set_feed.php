<?php
//$id_feed = -1;
$error = 0;
$feed_title = '';
$feed_url = '';
$feed_refresh = '';

if(isset($_POST['feed_title'])){
	$feed_title = $_POST['feed_title'];
}
if(isset($_POST['feed_url'])){
	$feed_url = $_POST['feed_url'];
}
if(isset($_POST['feed_refresh']) && $_POST['feed_refresh'] != '' && is_numeric($_POST['feed_refresh']) && $_POST['feed_refresh'] > 0){
	$feed_refresh = $_POST['feed_refresh'];
}


if($id_feed > 0){
	mysql_query("
		update t_feed
		set
			title = '" . mysql_real_escape_string($feed_title) . "',
			url = '" . mysql_real_escape_string($feed_url) . "',
			refresh = " . ($feed_refresh == '' ? 'NULL' : $feed_refresh) . "
		where
			id_feed = " . $id_feed . "
		", $conn);
}
else {
	mysql_query("
		insert into t_feed
		(
			title,
			url,
			refresh
		)
		values
		(
			'" . mysql_real_escape_string($feed_title) . "',
			'" . mysql_real_escape_string($feed_url) . "',
			" . ($feed_refresh == '' ? 'NULL' : $feed_refresh) . "
		)
		", $conn);
}
?>