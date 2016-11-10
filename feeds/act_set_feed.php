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
if(isset($_POST['feed_parser'])){
	$feed_parser = $_POST['feed_parser'];
}
if(isset($_POST['feed_parse_max_items']) && $_POST['feed_parse_max_items'] != '' && is_numeric($_POST['feed_parse_max_items']) && $_POST['feed_parse_max_items'] > 0){
	$feed_parse_max_items = $_POST['feed_parse_max_items'];
}


if($id_feed > 0){
	mysqli_query($conn, "
		update t_feed
		set
			title = '" . mysqli_real_escape_string($conn, $feed_title) . "',
			url = '" . mysqli_real_escape_string($conn, $feed_url) . "',
			refresh = " . ($feed_refresh == '' ? 'NULL' : $feed_refresh) . ",
			parser = '" . mysqli_real_escape_string($conn, $feed_parser) . "',
			parse_max_items = " . ($feed_parse_max_items == '' ? 'NULL' : $feed_parse_max_items) . "
		where
			id_feed = " . $id_feed . "
			and id_user = " . $_SESSION['user_id'] . "
		");
}
else {
	mysqli_query($conn, "
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
			'" . mysqli_real_escape_string($conn, $feed_title) . "',
			'" . mysqli_real_escape_string($conn, $feed_url) . "',
			" . ($feed_refresh == '' ? 'NULL' : $feed_refresh) . ",
			'" . mysqli_real_escape_string($conn, $feed_parser) . "',
			" . ($feed_parse_max_items == '' ? 'NULL' : $feed_parse_max_items) . "
		)
		");
}
?>