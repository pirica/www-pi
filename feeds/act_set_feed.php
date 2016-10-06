<?php
//$id_feed = -1;
$error = 0;
$feed_title = '';
$feed_url = '';
$feed_refresh = '';
$feed_parser = '';
$feed_parse_max_items = '';

if(isset($_POST['feed_title'])){
	$feed_title = $_POST['feed_title'];
}
if(isset($_POST['feed_url'])){
	$feed_url = $_POST['feed_url'];
}
if(isset($_POST['feed_refresh']) && $_POST['feed_refresh'] != '' && is_numeric($_POST['feed_refresh']) && $_POST['feed_refresh'] > 0){
	$feed_refresh = $_POST['feed_refresh'];
}
if(isset($_POST['parser'])){
	$feed_parser = $_POST['parser'];
}
if(isset($_POST['parse_max_items']) && $_POST['parse_max_items'] != '' && is_numeric($_POST['parse_max_items']) && $_POST['parse_max_items'] > 0){
	$feed_parse_max_items = $_POST['parse_max_items'];
}


if($id_feed > 0){
	mysql_query("
		update t_feed
		set
			title = '" . mysql_real_escape_string($feed_title) . "',
			url = '" . mysql_real_escape_string($feed_url) . "',
			refresh = " . ($feed_refresh == '' ? 'NULL' : $feed_refresh) . ",
			parser = '" . mysql_real_escape_string($feed_parser) . "',
			parse_max_items = " . ($feed_parse_max_items == '' ? 'NULL' : $feed_parse_max_items) . "
		where
			id_feed = " . $id_feed . "
			and id_user = " . $_SESSION['user_id'] . "
		", $conn);
}
else {
	mysql_query("
		insert into t_feed
		(
			id_user,
			title,
			url,
			refresh,
			parser,
			parse_max_items
		)
		values
		(
			" . $_SESSION['user_id'] . ",
			'" . mysql_real_escape_string($feed_title) . "',
			'" . mysql_real_escape_string($feed_url) . "',
			" . ($feed_refresh == '' ? 'NULL' : $feed_refresh) . ",
			'" . mysql_real_escape_string($feed_parser) . "',
			" . ($feed_parse_max_items == '' ? 'NULL' : $feed_parse_max_items) . "
		)
		", $conn);
}
?>