<?php

mysqli_query($conn, "
	update t_app_action_data
	set
		active = 2
	where 
		id_app = " . $app->getId() . "
		and code = 'details'
	");

mysqli_query($conn, "

	replace into t_app_action_data
	(
		id_app_action_data,
		id_app,
		active,
		code,
		id_user,
		sort_order,
		url,
		description
	)

	select
		concat(a.id_app, '-', aa.code, '-', s.id_share, '-', u.id_user)as id_app_action_data,
		a.id_app,
		1 as active,
		aa.code,
		u.id_user,
		NULL as sort_order,
		concat('id_share=', s.id_share) as url,
		s.name as description
		
	from 
		users.t_app a
		join users.t_app_action aa on aa.id_app = a.id_app and aa.code = 'details'
		join filerep.t_share s on s.active = 1
		join users.t_user u on u.active = 1
		join users.t_profile p on p.id_profile = u.id_profile and p.active = 1
	
	where
		a.relative_url = '/filerep'
		and (p.full_access = 1 or s.id_user = u.id_user)
		
	order by
		description
	
	");
	
mysqli_query($conn, "
	update t_app_action_data
	set
		active = 0
	where 
		id_app = " . $app->getId() . "
		and code = 'details'
		and active = 2
	");

?>