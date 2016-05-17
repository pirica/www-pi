<?php

$request_uri = '';
if(isset($_SERVER['REQUEST_URI'])){
	$request_uri = $_SERVER['REQUEST_URI'];
}

if($request_uri == '' || $request_uri == '/'){
	$request_uri = '/';
}
else {
	$request_uri = '/' . explode("/", $request_uri)[1];
}
//echo '<!--u:'.$request_uri.'-->';

$qry_apps = mysql_query("
	
	select
		a.id_app,
		a.description,
		'' as info,
		a.relative_url,
		a.show_in_overview,
		a.show_in_topmenu,
		a.login_required,
		
		case when '" . mysql_real_escape_string($request_uri) . "' = a.relative_url then 1 else 0 end as is_current
		
	from t_app a
		join t_profile p on p.id_profile = " . $id_profile . "
		left join t_profile_app pa on pa.id_app = a.id_app and pa.id_profile = p.id_profile
	where
		pa.allowed = 1 or p.full_access = 1
	order by
		ifnull(a.sort_order, a.id_app)
		
	", $conn_users);
	
	
$qry_actions = mysql_query("
	
	select
		ifnull(a.id_app, -1) as id_app,
		ifnull(a.description, 'Global') as appname,
		
		s.id_app_action,
		s.code,
		s.page_title,
		s.login_required
		
	from t_app_action s
	left join t_app a on a.id_app = s.id_app
	where
		s.active = 1
		
	order by
		ifnull(a.sort_order, ifnull(a.id_app, -1)),
		ifnull(s.code,'Main')
		
		
	", $conn_users);
	
	
?>