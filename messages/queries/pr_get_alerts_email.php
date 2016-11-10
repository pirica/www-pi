<?php
	
$qry_alerts_email = mysqli_query($conn, "
	
	select
		ae.id_alert_email,
		ae.description,
		ae.when_from,
		ae.when_subject,
		ae.enabled,
		
		aer.id_alert_email_result,
		aer.result,
		aer.date_result
		
	from t_alert_email ae
		join t_alert_email_result aer on aer.id_alert_email = ae.id_alert_email
		
	where
		DATE_FORMAT(aer.date_result, '%Y%m%d') >= DATE_FORMAT(now() - interval " . $settings->val('email_alerts_show_months', 6) . " month, '%Y%m%d')
	
	order by
		ae.description,
		aer.id_alert_email_result desc
		
		
	");
	
?>