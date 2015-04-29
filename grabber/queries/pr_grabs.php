<?php
	
$qry_grabs = mysql_query("
	
	select
		g.id_grab,
		g.description,
		g.enabled,
		g.files_building,
		g.running,
		g.max_grabbers,
		g.url,
		g.path,
		g.filename,
		ifnull(g.excluded,'') as excluded,
		
		ifnull(count(distinct gc.id_grab_counter), 0) as counters_total,
		
		exp(sum(log(coalesce(
			case gc.type
				when 'int' then gc.intto - gc.intfrom
				when 'list' then (LENGTH(listvalues) - LENGTH(REPLACE(listvalues, ',', '')))/LENGTH(',') + 1
				when 'date' then ifnull(gc.dateto, now()) - gc.datefrom
			end
		,1)))) as countvalues,
		
		g.date_last_run,
		g.date_last_action,
		
		ifnull(g.files_total, 0) as files_total,
		ifnull(g.files_done, 0) as files_done,
		ifnull(g.files_exist, 0) as files_exist,
		ifnull(g.files_todo, 0) as files_todo,
		ifnull(g.files_notfound, 0) as files_notfound,
		ifnull(g.files_timeout, 0) as files_timeout,
		ifnull(g.files_empty, 0) as files_empty,
		ifnull(g.files_error, 0) as files_error,
		ifnull(g.files_excluded, 0) as files_excluded,
		
		now() + interval (files_todo / ifnull(max_grabbers,100) * 5) minute as eta,
		
		FROM_UNIXTIME(((UNIX_TIMESTAMP(
			(now() + interval (files_todo / ifnull(max_grabbers,100) * 5) minute)
		) + 300) DIV 300) * 300)  AS eta_rounded

		
	from t_grab g
		left join t_grab_counter gc on gc.id_grab = g.id_grab
			and gc.active = 1
	where
		g.active = 1
	
	group by
		g.id_grab,
		g.description,
		g.enabled,
		g.files_building,
		g.running,
		g.max_grabbers,
		g.url,
		g.path,
		g.filename,
		g.excluded,
		g.date_last_run,
		g.date_last_action,
		g.files_total,
		g.files_done,
		g.files_exist,
		g.files_todo,
		g.files_notfound,
		g.files_timeout,
		g.files_empty,
		g.files_error,
		g.files_excluded
		
	", $conn);
	
?>