<?php

/*

Range start = included
Range end = excluded

*/

$date = saneInput('date', 'string', '');
$show = saneInput('show', 'string', '');
$tm = saneInput('tm', 'int', '0');

if(!in_array($show, array('total','down','up','both','all'))){
	$show = 'total';
}
if($tm != 0 || $tm != 1){
	$tm = 0;
}

/*
$night_start = '00:00';
$night_end = '10:00';
*/
$night_start = $settings->val('telemeter_night_start', '00:00');
$night_end = $settings->val('telemeter_night_end', '10:00');

$date = saneInput('date', 'string', '');
if($date != ''){
	$date = (new DateTime($date))->getTimestamp();
}
else {
	$date = time();
}

$date_prev = '';
$date_next = '';


switch($action->getCode()){
	case 'usage_now':
		$app->setTitle('Usage now');
		$date = time(); // current hour, since this is 'now'
		
		$date_period_format = '%Y-%m-%d %H:%i';
		$date_label_format = '%H:%i';
		$date_period = 'minute';
		
		$range_start = date("Y-m-d H:00", $date - 3600);
		$range_end = date("Y-m-d H:i", $date + 60);

		break;
	
	case 'usage_today':
		$app->setTitle('Usage today');
		
		$date_period_format = '%Y-%m-%d %H';
		$date_label_format = '%H';
		$date_period = 'hour';
		
		$range_start = date("Y-m-d 00", strtotime('-1 day', $date));
		$range_end = date("Y-m-d H", strtotime('+1 hour', $date));

		$date_prev = date("Y-m-d", strtotime('-1 day', $date));
		$date_next = date("Y-m-d", strtotime('+1 day', $date));
		
		break;
	
	case 'usage_day':
		$app->setTitle('Usage per day');
		
		$date_period_format = '%Y-%m-%d';
		$date_label_format = '%d';
		$date_period = 'day';
		
		// if today is before the period start, check last month (which is still current period)
		if(date("d", $date) < 4 ){
			$range_start = date("Y-m-", strtotime('-1 month', $date)) . '04';
			$range_end = date("Y-m-", $date) . '04';
			
			$date_prev = date("Y-m-d", strtotime('-2 month', $date));
			$date_next = date("Y-m-d", strtotime('+1 month', $date));
		}
		// else the current month
		else {
			$range_start = date("Y-m-", $date) . '04';
			$range_end = date("Y-m-", strtotime('+1 month', $date)) . '04';
			
			$date_prev = date("Y-m-d", strtotime('-1 month', $date));
			$date_next = date("Y-m-d", strtotime('+1 month', $date));
		}
		
		
		break;
	
	case 'usage_month':
		$app->setTitle('Usage per month');
		
		$date_period_format = '%Y-%m';
		$date_label_format = '%b';
		$date_period = 'month';
		
		$range_start = date("Y-m", strtotime('-1 year', $date));
		$range_end = date("Y-m", strtotime('+1 month', $date));
		
		$date_prev = date("Y-m-d", strtotime('-1 year', $date));
		$date_next = date("Y-m-d", strtotime('+1 year', $date));
		
		break;
}


/*
echo date("Y-m-d H:i:s", $range_start);
echo date("Y-m-d H:i:s", $range_end);
*/

// http://css-tricks.com/snippets/css/a-guide-to-flexbox/
// http://bennettfeely.com/flexplorer/


$night_start_sql = str_replace(':', '', $night_start);
$night_end_sql = str_replace(':', '', $night_end);

$range_start_sql = str_replace(':', '', str_replace('-', '', str_replace(' ', '', $range_start)));
$range_end_sql = str_replace(':', '', str_replace('-', '', str_replace(' ', '', $range_end)));
$date_range_format = str_replace(':', '', str_replace('-', '', str_replace(' ', '', $date_period_format)));


//require 'queries/pr_get_hosts.php';
//require 'queries/pr_get_hosts_usage.php';


switch($action->getCode()){
	case 'usage_now':
		//require 'queries/pr_set_hosts_usage_now.php';
		require 'queries/pr_get_usage_now.php';
		break;
	
	case 'usage_today':
		//require 'queries/pr_set_hosts_usage_today.php';
		require 'queries/pr_get_usage_today.php';
		break;
	
	case 'usage_day':
		require 'queries/pr_get_usage_day.php';
		break;
	
	case 'usage_month':
		require 'queries/pr_get_usage_month.php';
		break;
}

?>