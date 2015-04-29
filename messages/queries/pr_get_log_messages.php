<?php
	
$qry_log_messages = mysql_query("
	
	select
		lm.id_log_message,
		lm.date_sent,
		lm.type,
		lm.host,
		lm.channel,
		lm.title,
		lm.message,
		lm.success
		
	from t_log_message lm
	where
		DATE_FORMAT(lm.date_sent, '%Y%m%d') >= DATE_FORMAT(now() - interval 7 day, '%Y%m%d')
	
	order by
		lm.date_sent desc,
		lm.id_log_message desc
		
		
	", $conn);
	
?>