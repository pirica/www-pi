<?php

/*

Range start = included
Range end = excluded

*/

$date = saneInput('date', 'string', '');
$show = saneInput('show', 'string', '');
$tm = saneInput('tm', 'int', '-1');

if(!in_array($show, array('total','down','up','both','all'))){
	$show = $settings->val('usage_show_default_value', 'total');
}
if($tm != 0 || $tm != 1){
	$tm = $settings->val('usage_telemeter_default_value', 0);
}

$tm_start = $settings->val('telemeter_period_startday', 4);
$tm_start0 = ($tm_start < 10 ? '0' : '') . $tm_start;

//$night_start = $settings->val('telemeter_night_start', '00:00');
//$night_end = $settings->val('telemeter_night_end', '10:00');

$date = saneInput('date', 'string', '');
/*if($date != ''){
	$date = (new DateTime($date))->getTimestamp();
}
else {
	$date = time();
}*/

$date_prev = '';
$date_next = '';

$subaction = '';
$subdate = '';

switch($action->getCode()){
	case 'usage_now':
		if($date != ''){
			$date = (new DateTime($date . ':00'))->getTimestamp();
		}
		else {
			$date = time();
		}
		$date = time(); // current hour, since this is 'now'
		
		$date_period_format = '%Y-%m-%d %H:%i';
		$date_label_format = '%H:%i';
		$date_period = 'minute';
		
		$range_start = date("Y-m-d H:00", $date - 3600);
		$range_end = date("Y-m-d H:i", $date + 60);
		
		break;
	
	case 'usage_today':
		if($date != ''){
			$date = (new DateTime($date . ' 00:00'))->getTimestamp();
		}
		else {
			$date = time();
		}
		$date_period_format = '%Y-%m-%d %H';
		$date_label_format = '%H';
		$date_period = 'hour';
		
		$range_start = date("Y-m-d 00", strtotime('-1 day', $date));
		$range_end = date("Y-m-d H", strtotime('+1 hour', $date));

		$date_prev = date("Y-m-d", strtotime('-1 day', $date));
		$date_next = date("Y-m-d", strtotime('+1 day', $date));
		
		$subaction = 'usage_now';
		$subdate = ':00';

		break;
	
	case 'usage_day':
		if($date != ''){
			$date = (new DateTime($date))->getTimestamp();
		}
		else {
			$date = time();
		}
		$date_period_format = '%Y-%m-%d';
		$date_label_format = '%d';
		$date_period = 'day';
		
		// if today is before the period start, check last month (which is still current period)
		if(date("d", $date) < $tm_start ){
			$range_start = date("Y-m-", strtotime('-1 month', $date)) . $tm_start0;
			$range_end = date("Y-m-", $date) . $tm_start0;
			
			$date_prev = date("Y-m-d", strtotime('-2 month', $date));
			$date_next = date("Y-m-d", strtotime('+1 month', $date));
		}
		// else the current month
		else {
			$range_start = date("Y-m-", $date) . $tm_start0;
			$range_end = date("Y-m-", strtotime('+1 month', $date)) . $tm_start0;
			
			$date_prev = date("Y-m-d", strtotime('-1 month', $date));
			$date_next = date("Y-m-d", strtotime('+1 month', $date));
		}
		
		$subaction = 'usage_today';
		$subdate = ' 00:00';

		break;
	
	case 'usage_month':
		if($date != ''){
			$date = (new DateTime($date . '-' . $tm_start0))->getTimestamp();
		}
		else {
			$date = time();
		}
		$date_period_format = '%Y-%m';
		$date_label_format = '%b';
		$date_period = 'month';
		
		$range_start = date("Y-m", strtotime('-1 year', $date));
		$range_end = date("Y-m", strtotime('+1 month', $date));
		
		$date_prev = date("Y-m", strtotime('-1 year', $date));
		$date_next = date("Y-m", strtotime('+1 year', $date));
		
		$subaction = 'usage_day';
		$subdate = '-' . $tm_start0;

		break;
}


/*
echo date("Y-m-d H:i:s", $range_start);
echo date("Y-m-d H:i:s", $range_end);
*/

// http://css-tricks.com/snippets/css/a-guide-to-flexbox/
// http://bennettfeely.com/flexplorer/


//$night_start_sql = str_replace(':', '', $night_start);
//$night_end_sql = str_replace(':', '', $night_end);

$range_start_sql = str_replace(':', '', str_replace('-', '', str_replace(' ', '', $range_start)));
$range_end_sql = str_replace(':', '', str_replace('-', '', str_replace(' ', '', $range_end)));
$date_range_format = str_replace(':', '', str_replace('-', '', str_replace(' ', '', $date_period_format)));


$filter_macs = '';

switch($action->getCode()){
	case 'usage_now':
		require 'queries/pr_get_hosts_usage_now.php';
		break;
	
	case 'usage_today':
		require 'queries/pr_get_hosts_usage_today.php';
		break;
	
	case 'usage_day':
		require 'queries/pr_get_hosts_usage_day.php';
		break;
	
	case 'usage_month':
		require 'queries/pr_get_hosts_usage_month.php';
		break;
}

while($hosts_usage = mysql_fetch_array($qry_hosts_usage)){
	if($filter_macs != ''){
		$filter_macs .= ",";
	}
	$filter_macs .= "'" . $hosts_usage['mac_address'] . "'";
}


switch($action->getCode()){
	case 'usage_now':
		require 'queries/pr_get_usage_now.php';
		break;
	
	case 'usage_today':
		require 'queries/pr_get_usage_today.php';
		break;
	
	case 'usage_day':
		require 'queries/pr_get_usage_day.php';
		break;
	
	case 'usage_month':
		require 'queries/pr_get_usage_month.php';
		break;
}


$section_width = 100; // .section-label
if($show == 'total' || $show == 'all'){
	$section_width += 16 + 1; // .usage-total
}
if($show == 'down' || $show == 'both' || $show == 'all'){
	$section_width += 8 + 1; // .usage-down
}
if($show == 'up' || $show == 'both' || $show == 'all'){
	$section_width += 8 + 1; // .usage-up
}
if($show == 'both' || $show == 'all'){
	$section_width += 8; // .usage-spacer
}
$section_width .= mysql_num_rows($qry_totals);

?>