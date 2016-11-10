<?php
	
$qry_grabs = mysqli_query($conn, "
	
	select
		g.id_grab,
		g.description,
		g.enabled,
		g.files_building,
		g.running,
		g.url,
		g.path,
		g.filename,
		
		ifnull(g.max_grabbers,-1) as max_grabbers,
		ifnull(g.excluded,'') as excluded,
		ifnull(g.excluded_size,-1) as excluded_size,
		ifnull(g.always_retry,0) as always_retry,
		ifnull(g.script_completion,'') as script_completion,
		ifnull(g.remove_completed_after_days,-1) as remove_completed_after_days,
		ifnull(g.remove_inactive_after_months,-1) as remove_inactive_after_months,
		ifnull(g.keep_diskspace_free,-1) as keep_diskspace_free,
		ifnull(g.scheduled,0) as scheduled,
		
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
		
		now() + interval (files_todo / ifnull(max_grabbers," . $settings->val('grabber_maxgrabbers_default', 20) . ") * " . $settings->val('grabber_run_interval', 5) . ") minute as eta,
		
		FROM_UNIXTIME(((UNIX_TIMESTAMP(
			(now() + interval (files_todo / ifnull(max_grabbers," . $settings->val('grabber_maxgrabbers_default', 20) . ") * " . $settings->val('grabber_run_interval', 5) . ") minute)
		) + (" . $settings->val('grabber_run_interval', 5) . " * 60)) DIV (" . $settings->val('grabber_run_interval', 5) . " * 60)) * (" . $settings->val('grabber_run_interval', 5) . " * 60))  AS eta_rounded

		
	from t_grab g
		left join t_grab_counter gc on gc.id_grab = g.id_grab
			and gc.active = 1
	where
		g.id_user = " . $_SESSION['user_id'] . "
		and g.active = 1
	
	group by
		g.id_grab,
		g.description,
		g.enabled,
		g.files_building,
		g.running,
		g.url,
		g.path,
		g.filename,
		
		g.max_grabbers,
		g.excluded,
		g.excluded_size,
		g.always_retry,
		g.script_completion,
		g.remove_completed_after_days,
		g.remove_inactive_after_months,
		g.keep_diskspace_free,
		g.scheduled,
		
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
		
	");
	
?>