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

$qry_apps = $mysqli->prepare("
	
	select
		a.id_app,
		a.description,
		'' as info,
		a.relative_url,
		a.show_in_overview,
		a.show_in_topmenu,
		a.login_required,
		
		case when ? = a.relative_url then 1 else 0 end as is_current
		
	from t_app a
		join t_profile p on p.id_profile = ?
		left join t_profile_app pa on pa.id_app = a.id_app and pa.id_profile = p.id_profile
	where
		pa.allowed = 1 or p.full_access = 1
	order by
		ifnull(a.sort_order, a.id_app)
		
	");
	
$qry_apps->bind_param('si', $request_uri, $id_profile);
$qry_apps->execute();
$qry_apps->store_result();

//if ($qry_apps->num_rows == 1) {
$qry_apps->bind_result(
	$id_app,
	$description,
	$info,
	$relative_url,
	$show_in_overview,
	$show_in_topmenu,
	$login_required,
	$is_current
);
	
?>