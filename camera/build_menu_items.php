<?php

require dirname(__FILE__).'/../_core/appinit.php';

require 'connection.php';
require 'functions.php';

mysqli_query($conn, "
	update users.t_app_action_data
	set
		active = 2
	where 
		id_app = " . $app->getId() . "
		and code = 'camera'
	");

mysqli_query($conn, "
	
	replace into users.t_app_action_data
	(
		id_app_action_data,
		id_app,
		active,
		code,
		sort_order,
		url,
		description
	)

	select

		concat(a.id_app, '-', aa.code, '-', -1)as id_app_action_data,
		a.id_app,
		1 as active,
		aa.code,
		-1 as sort_order,
		'id_camera=-1' as url,
		'All cameras' as description
		
	from 
		users.t_app a
		join users.t_app_action aa on aa.id_app = a.id_app and aa.code = 'camera'
	
	where
		a.relative_url = '/camera'
		
	");
	
mysqli_multi_query($conn, "
	SET @rank=0;

	replace into users.t_app_action_data
	(
		id_app_action_data,
		id_app,
		active,
		code,
		sort_order,
		url,
		description
	)

	select
		concat(a.id_app, '-', aa.code, '-', c.id_camera)as id_app_action_data,
		a.id_app,
		1 as active,
		aa.code,
		@rank:=@rank+1 as sort_order,
		concat('id_camera=', c.id_camera) as url,
		concat(c.description, case when c.is_local = 1 then ' (local)' else '' end) as description
		
	from 
		users.t_app a
		join users.t_app_action aa on aa.id_app = a.id_app and aa.code = 'camera'
		join router.t_camera c on 1 = 1
		
	where
		a.relative_url = '/camera'
		
	order by
		sort_order,
		description
		
	");
	
mysqli_query($conn, "
	update users.t_app_action_data
	set
		active = 0
	where 
		id_app = " . $app->getId() . "
		and code = 'camera'
		and active = 2
	");

?>