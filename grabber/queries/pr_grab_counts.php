<?php
	
$qry_grab_counts = mysql_query("
	
	select
		gc.id_grab_counter,
		gc.active,
		
		case gc.type
			when 'int' then gc.intto - gc.intfrom
			when 'list' then (LENGTH(listvalues) - LENGTH(REPLACE(listvalues, ',', '')))/LENGTH(',') + 1
			when 'date' then ifnull(gc.dateto, now()) - gc.datefrom
		end as count,
		
		gc.type,
		gc.field,
		
		gc.datefrom,
		gc.dateto,
		
		gc.intfrom,
		gc.intto,
		
		gc.listvalues
		
	from t_grab_counter gc
	where
		gc.id_grab = " . $id_grab . "
		
	order by
		ifnull(gc.sort_order, gc.id_grab_counter)
	
	", $conn);
	
?>