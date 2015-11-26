<?php
require 'connection.php';
require 'functions.php';
require dirname(__FILE__).'/../_core/appinit.php';

$qry = mysql_query("
	select
		t.id_tracktrace,
		t.tracking_code,
		t.postal_code,
		t.title,
		tt.description,
		tt.template,
		tt.disable_when
		
	from t_tracktrace t
		join t_tracktrace_type tt on tt.id_tracktrace_type = t.id_tracktrace_type
			and tt.active = 1
			
	where
		t.enabled = 1
		
	");

while($tt = mysql_fetch_array($qry)){
	
	$qry_result = mysql_query("
		select 
			result
		from t_tracktrace_result
		where
			id_tracktrace = " . $tt['id_tracktrace'] . "
		order by
			id_tracktrace_result desc
		limit 1
		");

	$status = '';
	$status_changed = false;
	$msg = '';
	
	while($ttresult = mysql_fetch_array($qry_result)){
		$status = $ttresult['result'];
	}

	include ('tracktrace/' . $tt['template']);
	
	if($msg != $status && $msg != ''){
		$status_changed = true;
	}
	
	if($status_changed && $msg != ''){
		
		mysql_query("
			insert into t_tracktrace_result
			(
				id_tracktrace,
				result,
				date_result
			)
			values
			(
				" . $tt['id_tracktrace'] . ",
				'" . mysql_real_escape_string($msg) . "',
				now()
			)
			");

		$channel = $tt['description'];
		$priority = $settings->val('tracktrace_alerting_priority', 1);
		send_msg($channel, $tt['title'], $msg, $priority);
		
		
		if(stripos($msg, $tt['disable_when']) !== false){
			// completed
			mysql_query("
				update t_tracktrace
				set enabled = 0
				where
					id_tracktrace = " . $tt['id_tracktrace'] . "
				");
		}
			
	}
	
}

?>