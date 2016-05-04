<?php

$qry_mng_profile_apps = $mysqli->prepare("
	
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
	
//$qry_mng_profile_apps->bind_param('s', $request_uri);
$qry_mng_profile_apps->execute();
$qry_mng_profile_apps->store_result();

$qry_mng_profile_apps->bind_result(
	$id_profile,
	$profilename,
	$full_access,
	
	$id_app,
	$appname,
	
	$id_profile_app,
	$allowed
);
	
		
$profiledata = [];

$prev_id_profile = -2;
while ($qry_mng_profile_apps->fetch()) {
	if($prev_id_profile != $id_profile){
		$profiledata[] = [];
		$prev_id_profile = $id_profile;
	}
	$profiledata[count($profiledata) - 1][] = array(
		'id_profile' => $id_profile,
		'profilename' => $profilename,
		'full_access' => $full_access,
		
		'id_app' => $id_app,
		'appname' => $appname,
		
		'id_profile_app' => $id_profile_app,
		'allowed' => $allowed
	);
}

?>