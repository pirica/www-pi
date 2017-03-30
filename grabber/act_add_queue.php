<?php

$url = saneInput('url');

if($url != '' && stripos($url, 'http') === 0){
	mysqli_query($conn, "
		insert into t_queue
		(
			url
		)
		values
		(
			'" . mysqli_real_escape_string($conn, $url) . "'
		)
		");
		
}
?>