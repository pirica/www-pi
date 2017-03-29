<?php

$grab_url = saneInput('url');

if($grab_url != ''){
	mysqli_query($conn, "
		insert into t_queue
		(
			url
		)
		values
		(
			'" . mysqli_real_escape_string($conn, $grab_url) . "'
		)
		");
		
}
?>