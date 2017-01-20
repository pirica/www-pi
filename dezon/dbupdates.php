<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connection.php';
include 'functions.php';

$tables = ['t_team', 't_bijscholing', 't_pagina'];

for($i=0; $i<count($tables); $i++)
{
	$qry = mysqli_query($conn, "
		select * from " . $tables[$i] . "
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
			$columns .= ($columns == '' ? '' : ',') . $column;
			$data .= ($data == '' ? '' : ',') . "'" . mysqli_real_escape_string($conn, $data) . "'";
		}
		echo "replace into " . $tables[$i] . " (" . $columns . ") values (" . $data . ");\r\n";
	}
}

?>