<?php
$id_profile = saneInput('id_profile', 'int', -1);
$id_app = saneInput('id_app', 'int', -1);
$field = saneInput('field', 'string');
$value = saneInput('value', 'string');

if(!in_array($field, array('allowed'))){
	$field = '';
}

mysql_query("
	insert into t_profile_app
	(
		id_profile,
		id_app,
		allowed
	)
	select
		p.id_profile,
		a.id_app,
		0 as allowed
		
	from t_profile p
	cross join t_app a
	left join t_profile_app pa on pa.id_profile = p.id_profile and pa.id_app = a.id_app
	where
		p.active = 1
		and p.full_access = 0
		and pa.id_profile_app is null
		
	", $conn_users);
	
$error = 0;
if($id_app > 0 && $id_profile > 0 && $field != ''){
	
	mysql_query("
		update t_profile_app
		set
			" . $field . " = '" . mysql_real_escape_string($value) . "'
			
		where
			id_app = " . $id_app . "
			and id_profile = " . $id_profile . "
			and active = 1
			
		", $conn_users);
		
}

?>