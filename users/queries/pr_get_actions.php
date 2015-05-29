<?php

$qry_mng_actions = $mysqli->prepare("
	
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
		
	");
	
//$qry_mng_actions->bind_param('s', $request_uri);
$qry_mng_actions->execute();
$qry_mng_actions->store_result();

$qry_mng_actions->bind_result(
	$id_app,
	$appname,
	
	$id_app_action,
	$code,
	$page_title,
	$login_required
);
	
		
$actionsdata = [];
/*
	$id_app,
	$appname,
	
	$id_app_action,
	$code,
	$page_title,
	$login_required
*/
$prev_id_app = -1;
while ($qry_mng_actions->fetch()) {
	if($prev_id_app != $id_app){
		$actionsdata[] = [];
		$prev_id_app = $id_app;
	}
	$actionsdata[count($actionsdata) - 1][] = array(
		'id_app' => $id_app,
		'appname' => $appname,
		
		'id_app_action' => $id_app_action,
		'code' => $code,
		'page_title' => $page_title,
		'login_required' => $login_required
	);
}

?>