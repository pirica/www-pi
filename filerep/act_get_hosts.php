<?php


$sql = "
	select
		h.id_host,
		h.name,
		h.username,
		h.os,
		h.date_linked_since
	
	from t_host h
	where
		h.id_user = " . $id_user . "
		and h.active = 1
	";

$qry = mysql_query($sql, $conn);

$returnvalue = array('data' => mysql2json($qry));


?>