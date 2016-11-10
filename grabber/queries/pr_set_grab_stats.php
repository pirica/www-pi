<?php

mysqli_query($conn, "
	
	update t_grab g
	join (
		select
			gf.id_grab,
			count(gf.id_grab_file) as files_total,
			sum(case when ifnull(gf.status,'') in ('OK') then 1 else 0 end) as files_done,
			sum(case when ifnull(gf.status,'') in ('FX') then 1 else 0 end) as files_exist,
			sum(case when ifnull(gf.status,'') in ('', 'N', 'P') then 1 else 0 end) as files_todo,
			sum(case when ifnull(gf.status,'') in ('NF') then 1 else 0 end) as files_notfound,
			sum(case when ifnull(gf.status,'') in ('TO') then 1 else 0 end) as files_timeout,
			sum(case when ifnull(gf.status,'') in ('FE') then 1 else 0 end) as files_empty,
			sum(case when ifnull(gf.status,'') in ('E') then 1 else 0 end) as files_error,
			sum(case when ifnull(gf.status,'') in ('X') then 1 else 0 end) as files_excluded,
			
			max(case when ifnull(gf.status,'') not in ('', 'N') then gf.date_modified else 0 end) as date_last_action
		from t_grab_file gf
		where 
			gf.id_grab = " . $grabs['id_grab'] . "
			and gf.active = 1
		group by gf.id_grab
		
	) tgf  on tgf.id_grab = g.id_grab
	set
		g.files_total = tgf.files_total,
		g.files_done = tgf.files_done,
		g.files_exist = tgf.files_exist,
		g.files_todo = tgf.files_todo,
		g.files_notfound = tgf.files_notfound,
		g.files_timeout = tgf.files_timeout,
		g.files_empty = tgf.files_empty,
		g.files_error = tgf.files_error,
		g.files_excluded = tgf.files_excluded,
		
		g.date_last_action = tgf.date_last_action
		
	where
		g.id_grab = tgf.id_grab
		
");
	
?>