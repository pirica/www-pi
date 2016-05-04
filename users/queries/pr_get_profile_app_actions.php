<?php

$qry_mng_profile_apps = mysql_query("
	
	select
		p.id_profile,
		p.description as profilename,
		p.full_access,
		
		a.id_app,
		a.description as appname,
		
		aa.id_app_action,
		aa.code as appcode,
		aa.page_title,
		
		ifnull(paa.id_profile_app_action,-1) as id_profile_app_action,
		ifnull(paa.allowed,0) as allowed
		
	from t_profile p
		cross join t_app_action aa
		join t_app a on a.id_app = aa.id_app
		left join t_profile_app_action paa on paa.id_profile = p.id_profile and paa.id_app_action = aa.id_app_action
		
	where
		p.active = 1
		and aa.login_required = 1
		
	order by
		p.description,
		ifnull(a.sort_order, ifnull(a.id_app, -1)),
		aa.code
		
	", $conn_users);
	
		
$profiledata = [];

$prev_id_profile = -2;
while ($profile_apps = mysql_fetch_array($qry_mng_profile_apps)) {
	if($prev_id_profile != $profile_apps['id_profile']){
		$profiledata[] = [];
		$prev_id_profile = $profile_apps['id_profile'];
	}
	$profiledata[count($profiledata) - 1][] = array(
		'id_profile' => $profile_apps['id_profile'],
		'profilename' => $profile_apps['profilename'],
		'full_access' => $profile_apps['full_access'],
		
		'id_app' => $profile_apps['id_app'],
		'appname' => $profile_apps['appname'],
		
		'id_app_action' => $profile_apps['id_app_action'],
		'appcode' => $profile_apps['appcode'],
		'page_title' => $profile_apps['page_title'],
		
		'id_profile_app_action' => $profile_apps['id_profile_app_action'],
		'allowed' => $profile_apps['allowed']
	);
}

?>