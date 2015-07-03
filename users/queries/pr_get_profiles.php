<?php

$qry_mng_profiles = $mysqli->prepare("
	
	select
		p.id_profile,
		p.description,
		p.full_access,
		p.not_loggedin
		
	from t_profile p
	where
		p.active = 1
		
	order by
		p.description
		
	");
	
//$qry_mng_actions->bind_param('s', $request_uri);
$qry_mng_actions->execute();
$qry_mng_actions->store_result();

$qry_mng_actions->bind_result(
	$id_profile,
	$description,
	$full_access,
	$not_loggedin
);
	
		
$profilesdata = [];
while ($qry_mng_actions->fetch()) {
	$profilesdata[] = array(
		'id_profile' => $id_profile,
		'description' => $description,
		'full_access' => $full_access,
		'not_loggedin' => $not_loggedin,
		
		// for use in "settings" action
		'code' => $id_profile,
		'value' => $description
	);
}

?>