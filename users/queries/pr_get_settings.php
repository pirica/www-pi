<?php

$qry_mng_settings = $mysqli->prepare("
	
	select
		ifnull(a.id_app, -1) as id_app,
		ifnull(a.description, 'Global') as appname,
		
		s.id_setting,
		s.code,
		s.value,
		s.description,
		s.editable,
		s.edittype,
		s.extra,
		s.category,
		s.tooltip
		
	from t_setting s
	left join t_app a on a.id_app = s.id_app
	where
		s.active = 1
		
	order by
		ifnull(a.sort_order, ifnull(a.id_app, -1)),
		ifnull(s.sort_order, s.id_setting)
		
	");
	
//$qry_mng_settings->bind_param('s', $request_uri);
$qry_mng_settings->execute();
$qry_mng_settings->store_result();

$qry_mng_settings->bind_result(
	$id_app,
	$appname,
	
	$id_setting,
	$code,
	$value,
	$description,
	$editable,
	$edittype,
	$extra,
	$category,
	$tooltip
);
	
		
$settingsdata = [];
/*
	$id_app,
	$appname,
	
	$id_setting,
	$code,
	$value,
	$description,
	$editable,
	$edittype,
	$extra,
	$category,
	$tooltip
*/
$prev_id_app = -1;
while ($qry_mng_settings->fetch()) {
	if($prev_id_app != $id_app){
		$settingsdata[] = [];
		$prev_id_app = $id_app;
	}
	$settingsdata[count($settingsdata) - 1][] = array(
		'id_app' => $id_app,
		'appname' => $appname,
		
		'id_setting' => $id_setting,
		'code' => $code,
		'value' => $value,
		'description' => $description,
		'editable' => $editable,
		'edittype' => $edittype,
		'extra' => $extra,
		'category' => $category,
		'tooltip' => $tooltip
	);
}

?>