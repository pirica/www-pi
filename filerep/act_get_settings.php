<?php

$qry = mysqli_query($conn, "
	select
		s.code,
		ifnull(sh.value, s.value) as value,
		s.description,
		s.editable,
		s.edittype,
		s.extra,
		s.category,
		s.sort_order,
		s.tooltip
	from t_setting s 
		left join t_setting_host sh on sh.code = s.code 
			and sh.active = 1
			and sh.id_host = " . $id_host . " 
	where
		s.active = 1
	order by
		s.sort_order,
		s.code
	");
	
$returnvalue = array('data' => mysql2json($qry));

?>