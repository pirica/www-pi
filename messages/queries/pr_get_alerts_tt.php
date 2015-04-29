<?php
	
$qry_alerts_tt = mysql_query("
	
	
	select
		t.id_tracktrace,
		t.id_tracktrace_type,
		t.enabled,
		t.tracking_code,
		t.postal_code,
		t.title,
		
		tt.description,
		tt.template,
		tt.active,
		tt.disable_when,
		
		tr.id_tracktrace_result,
		ifnull(tr.result,'-No result received yet-') as result,
		tr.date_result
		
	from t_tracktrace t
		join t_tracktrace_type tt on tt.id_tracktrace_type = t.id_tracktrace_type
		left join t_tracktrace_result tr on tr.id_tracktrace = t.id_tracktrace
		
	where
		DATE_FORMAT(ifnull(tr.date_result,now()), '%Y%m%d') >= DATE_FORMAT(now() - interval 6 month, '%Y%m%d')
	
	order by
		t.id_tracktrace desc,
		tr.id_tracktrace_result desc
		
		
		
	", $conn);
	
?>