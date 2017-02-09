<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connections.php';
include 'functions.php';


shell_exec('if [ ! -d /var/www/html/dezon/data ]; then ln -s /var/docs/dezon /var/www/html/dezon/data > /dev/null 2>&1; fi');

$tables = array('t_team', 't_bijscholing', 't_pagina');

foreach($tables as $table)
{
	$qry = mysqli_query($conn, "
		select * from " . $table . "
		where 
			date_inserted > now() - interval 10 minute
			or date_modified > now() - interval 10 minute
			or date_deleted > now() - interval 10 minute
	");
	
	$disabled_columns = 'date_inserted,date_modified,date_deleted';
	
	while($result = mysqli_fetch_array($qry))
	{
		$columns = '';
		$data = '';
		foreach($result as $column=>$value)
		{
			if(!is_numeric($column) && strpos(','.$disabled_columns.',', ','.$column.',') === false)
			{
				if($column == 'active')
				{
					$column = 'actief';
				}
				if(strpos(','.$columns.',', ','.$column.',') === false)
				{
					$columns .= ($columns == '' ? '' : ',') . $column;
					$data .= ($data == '' ? '' : ',') . "'" . mysqli_real_escape_string($conn, $value) . "'";
				}
			}
		}
		echo "replace into " . $table . " (" . $columns . ") values (" . $data . ");\r\n";
	}
}

?>