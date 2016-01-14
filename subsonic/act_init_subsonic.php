<?php

require dirname(__FILE__).'/../_core/components/subsonic/Api.php';

$subsonic = new Subsonic(
	$settings->val('subsonic_server_username', 'admin'),
	$settings->val('subsonic_server_password', 'password'),
	$settings->val('subsonic_server_url', 'localhost'),
	$settings->val('subsonic_server_port', 4040),
	$settings->val('subsonic_clientname', 'subsonic_php')
);

?>