<?php

$qry_mng_profile_apps = mysqli_query($conn_users, "
	
	select
		p.id_profile,
		p.description as profilename,
		p.full_access,
		
		a.id_app,
		a.description as appname,
		
		ifnull(pa.id_profile_app,-1) as id_profile_app,
		ifnull(pa.allowed,0) as allowed
		
	from t_profile p
	cross join t_app a
	left join t_profile_app pa on pa.id_profile = p.id_profile and pa.id_app = a.id_app
	where
		p.active = 1
		
	order by
		p.description,
		ifnull(a.sort_order, ifnull(a.id_app, -1))
		
	");
	
		
$profiledata = [];

$prev_id_profile = -2;
while ($profile_apps = mysqli_fetch_array($qry_mng_profile_apps)) {
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
		
		'id_profile_app' => $profile_apps['id_profile_app'],
		'allowed' => $profile_apps['allowed']
	);
}

?>