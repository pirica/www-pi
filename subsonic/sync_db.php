<?php

set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connections.php';
require dirname(__FILE__).'/../_core/components/subsonic/Api.php';

$s = new Subsonic(
	$settings->val('subsonic_server_username', 'admin'),
	$settings->val('subsonic_server_password', 'password'),
	$settings->val('subsonic_server_url', 'localhost'),
	$settings->val('subsonic_server_port', 4040),
	$settings->val('subsonic_clientname', 'subsonic_php')
);

mysql_query("truncate table playlists");

$playlists = $s->getPlaylists();
$c_playlists = count($playlists);

for($i=0; i<$c_playlists; $i++){
	mysql_query("
		insert into playlists
		(
			id,
			name,
			comment,
			owner,
			public,
			songcount,
			duration,
			created
		)
		values 
		(
			" . $playlists[$i]['id'] . ",
			'" . $playlists[$i]['name'] . "',
			'" . $playlists[$i]['comment'] . "',
			'" . $playlists[$i]['owner'] . "',
			" . $playlists[$i]['public'] . ",
			" . $playlists[$i]['songcount'] . ",
			" . $playlists[$i]['duration'] . ",
			'" . $playlists[$i]['created'] . "'
		)
		");
}

?>