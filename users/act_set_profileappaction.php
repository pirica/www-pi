<?php
$id_profile = saneInput('id_profile', 'int', -1);
$id_app_action = saneInput('id_app_action', 'int', -1);
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

if(!in_array($field, array('allowed'))){
	$field = '';
}

mysqli_query($conn_users, "
	insert into t_profile_app_action
	(
		id_profile,
		id_app_action,
		allowed
	)
	select
		p.id_profile,
		a.id_app_action,
		0 as allowed
		
	from t_profile p
	cross join t_app_action a
	left join t_profile_app_action pa on pa.id_profile = p.id_profile and pa.id_app_action = a.id_app_action
	where
		p.active = 1
		and p.full_access = 0
		and pa.id_profile_app_action is null
		
	");
	
$error = 0;
if($id_app_action > 0 && $id_profile > 0 && $field != ''){
	
	mysqli_query($conn_users, "
		update t_profile_app_action
		set
			" . $field . " = '" . mysqli_real_escape_string($conn_users, $value) . "'
			
		where
			id_app_action = " . $id_app_action . "
			and id_profile = " . $id_profile . "
			
		");
		
}

?>