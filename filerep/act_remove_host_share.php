<?php
$logging = '';

$qry = mysql_query("
	update t_host_share 
	set
		active = 0,
		date_unlinked = now()
	where
		active = 1
		and id_host = " . $id_host . " 
		and id_share = " . $id_share . " 
	", $conn);

$returnvalue = array('type' => 'info', 'message' => 'share unlinked', 'logging' => $logging);


?>