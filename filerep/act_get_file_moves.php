<?php
set_time_limit(0);


$qry = mysqli_query($conn, "
	select
		fm.action,
		fm.source,
		fm.target

	from t_file_move fm
	
	where
		fm.id_share = " . $id_share . " 
		and fm.id_host = " . $id_host . " 
		
	");
$data = mysql2json($qry);

$returnvalue = array('data' => $data, 'logging' => '');

?>