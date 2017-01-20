<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connections.php';
include 'functions.php';

$tables = array('t_team', 't_bijscholing', 't_pagina');

foreach($tables as $table)
{
	$qry = mysqli_query($conn, "
		select * from " . $table . "
	");

	while($result = mysqli_fetch_array($qry))
	{
		$columns = '';
		$data = '';
		foreach($result as $column=>$value)
		{
			if($column == 'active')
			{
				$column = 'actief';
			}
			if(!is_numeric($column))
			{
				$columns .= ($columns == '' ? '' : ',') . $column;
				$data .= ($data == '' ? '' : ',') . "'" . mysqli_real_escape_string($conn, $value) . "'";
			}
		}
		echo "replace into " . $table . " (" . $columns . ") values (" . $data . ");\r\n";
	}
}

?>