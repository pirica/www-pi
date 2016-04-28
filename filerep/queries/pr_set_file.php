<?php

mysql_query("
	
	update t_file
	set
		rename_to = '" . mysql_real_escape_string($rename_to) . "'
	
	where
		id_file = " . $id_file . "
		and id_share = " . $id_share . "
		
	", $conn);
	
mysql_query("
	
	update t_directory
	set
		date_last_checked = null
	
	where
		id_share = " . $id_share . "
		and relative_directory in (
			select relative_directory
			from t_file
			where
				id_file = " . $id_file . "
				and id_share = " . $id_share . "
		)
		
	", $conn);
	
	
mysql_query("
	
	delete from t_file_move
	where
		id_file = " . $id_file . "
		
	", $conn);
	
mysql_query("
	
	insert into t_file_move
	(
		id_file,
		id_share,
		id_host,
		active,
		date_action,
		action,
		source,
		target
	)
	select
		f.id_file,
		f.id_share,
		hs.id_host,
		1 as active,
		now() as date_action,
		'move' as action,
		concat(f.relative_directory, f.filename) as source,
		concat(f.relative_directory, f.rename_to) as target
	from t_file f
	join t_host_share hs on hs.id_share = f.id_share and hs.active = 1
	where
		f.id_file = " . $id_file . "
		and f.id_share = " . $id_share . "
		and f.active = 1
		
	", $conn);
	
?>