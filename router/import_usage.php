<?php
set_time_limit(3600);
include "connection.php";
require dirname(__FILE__).'/../messages/functions.php';
require dirname(__FILE__).'/../_core/appinit.php';

/*

#delete from t_host_usage where id_host_usage in (select id_host_usage from (
select max(id_host_usage) as id_host_usage, count(id_host_usage) as nbrs, mac_address, date_usage, usage_peak_in, usage_peak_out
from t_host_usage
group by  mac_address, date_usage, usage_peak_in, usage_peak_out
having count(id_host_usage)  > 1
#)tmp)


2014-12-08

find /var/docs/router/2015-01-01 -type f -name 'usage_2015-01-01*' -exec cat {} + >> /var/docs/router_combined/2014-12-08/usage_2015-01-01.sql

rm /var/docs/router/usage_2015-01-01*

 
*/

$fulldir = '/var/docs/router';
$lockfile = 'import_usage.lock';

if (!file_exists( $fulldir . '/' . $lockfile)) {
	file_put_contents($fulldir . '/' . $lockfile, date('Y-m-d H:i:s', time()) );
	
//if(countProcesses($scriptName) <= 2){
	
	echo "Reading dir " . $fulldir . "<br>\r\n<br>\r\n";

	$handle = opendir($fulldir);
	flush();

	$counter = 0;

	while (($file = readdir($handle)) == true && $counter < 100){
		$fullfile = $fulldir . '/' . $file;
		if($file != '.' && $file != '..'){
			echo 'file: ' . $file;
			
            if(count(explode('_', $file)) == 3){
                $date = explode('_', $file)[1];
            }
            else {
                $date = date("Y-m-d", time());
            }
            
			$extarr = explode('.', $file);
			$extension = '.' . $extarr[count($extarr) - 1];
			
			if(!is_dir($fullfile) && ($extension == '.sql' || $extension == '.imported')){
				$counter++;
				
				$values = file_get_contents($fullfile);
				$valuesarr = explode(';', $values);
				$valuescount = count($valuesarr);
				$insertcount = 0;
				$emptycount = 0;
				for($i=0; $i<$valuescount; $i++){
					$qry = $valuesarr[$i];
					$qry = str_replace(' ', '', $qry);
					$qry = str_replace("\n", '', $qry);
					$qry = str_replace("\r", '', $qry);
					$qry = str_replace("\t", '', $qry);
					if($qry == ''){
						 $emptycount++;
					}
					else if(mysql_query($valuesarr[$i])){
						$insertcount++;
					}
				}
				//if($insertcount == $valuescount){
				if($insertcount > 0 || $emptycount > 0){
					echo ' imported';
					//if($extension == '.imported'){
						if (!file_exists( $fulldir . '/' . $date)) {
							mkdir( $fulldir . '/' . $date, 0777, true);
							echo ', dir ' . $date . ' created';
						}
						if(rename($fullfile, $fulldir . '/' . $date . '/' . $file)){
							echo ', moved';
						}
					//}
					/*if(unlink($fullfile)){
						echo ', deleted';
					}*/
					else if(rename($fullfile, $fullfile . '.imported')){
						//echo ', could not delete!';
						echo ', renamed';
					}
					else {
						echo ', could not rename!';
					}
					echo "<br>\r\n";
				}
				else {
					echo " error<br>\r\n";
					echo $values;
					echo "<br>\r\n";
				}
			}
			/*else if(!is_dir($fullfile) && $extension == '.imported'  ){//  && filemtime($fullfile) < time() - (24 * 60 * 60 * 32)){
				$counter++;
				
				/*if(unlink($fullfile)){
					echo ', deleted';
				}
				else if(rename($fullfile, $fullfile . '.imported')){
					echo ', could not delete!';
				}* /
				
				rename($fullfile, $fulldir . '/' . $date . '/' . $file);
				echo ', moved';
			}*/
			else {
				echo ' ignored';
				echo "<br>\r\n";
			}
			
		}
	}
	closedir( $handle ); 
	flush();

	
	$tm_start = $settings->val('telemeter_period_startday', 4);
	$tm_start0 = ($tm_start < 10 ? '0' : '') . $tm_start;

	require 'queries/pr_set_host_stats.php';
	
	require 'queries/pr_set_hosts_usage_now.php';
	require 'queries/pr_set_hosts_usage_today.php';
	
	$crondate = time();
	
	//if(date("H", $crondate) == 0 && date("i", $crondate) < 5)
	if(date("i", $crondate) % 5 == 0)
	{
		require 'queries/pr_set_hosts_usage_day.php';
		
		//if(date("d", $crondate) == $tm_start)
		{
			require 'queries/pr_set_hosts_usage_month.php';
		}
	}
	
	
	if(date("H", $crondate) > 7 && date("H", $crondate) < 22){
		
		require 'queries/pr_get_hosts.php';
		
		$total_traffic = 0;
		
		while($host = mysql_fetch_array($qry_hosts)){
			$total_traffic += ($host['downloaded_month'] + $host['uploaded_month']);
			
			if($host['alert_when_traffic_exceeds_daily'] > 0 && ($host['downloaded_today'] + $host['uploaded_today']) > $host['alert_when_traffic_exceeds_daily']){
				$date_check = date("Y-m-d 00:00:00");
				$channel = 'router';
				$title = 'Daily usage exceeded';
				$msg = 'Host ' . $host['hostname_lbl'] . ' exceeded it\'s daily usage of ' . formatFileSize($host['alert_when_traffic_exceeds_daily'], 0);
				$priority = $settings->val('messages_priority_daily_usage_exceeded', 1);
				if(!check_msg_already_sent($channel, $title, $msg, $date_check)){
					send_msg($channel, $title, $msg, $priority, 'import_usage');
				}
			}
			if($host['alert_when_traffic_exceeds_monthly'] > 0 && ($host['downloaded_month'] + $host['uploaded_month']) > $host['alert_when_traffic_exceeds_monthly']){
				if(date("d") < $tm_start ){
					$date_check = date("Y-m-" . $tm_start0 . " 00:00:00", strtotime('-1 month'));
				}
				else {
					$date_check = date("Y-m-" . $tm_start0 . " 00:00:00");
				}
				$channel = 'router';
				$title = 'Monthly usage exceeded';
				$msg = 'Host ' . $host['hostname_lbl'] . ' exceeded it\'s monthly usage of ' . formatFileSize($host['alert_when_traffic_exceeds_monthly'], 0);
				$priority = $settings->val('messages_priority_monthly_usage_exceeded', 2);
				if(!check_msg_already_sent($channel, $title, $msg, $date_check)){
					send_msg($channel, $title, $msg, $priority, 'import_usage');
				}
			}
		}
		
		if($settings->val('alert_when_total_traffic_exceeds', 0) > 0 && $total_traffic > $settings->val('alert_when_total_traffic_exceeds', 0)){
			if(date("d") < $tm_start ){
				$date_check = date("Y-m-" . $tm_start0 . " 00:00:00", strtotime('-1 month'));
			}
			else {
				$date_check = date("Y-m-" . $tm_start0 . " 00:00:00");
			}
			$channel = 'router';
			$title = 'Monthly usage exceeded';
			$msg = 'Total monthly usage of ' . formatFileSize($settings->val('alert_when_total_traffic_exceeds', 0), 0) . ' exceeded!';
			$priority = $settings->val('messages_priority_total_usage_exceeded', 2);
			if(!check_msg_already_sent($channel, $title, $msg, $date_check)){
				send_msg($channel, $title, $msg, $priority, 'import_usage');
			}
		}
	}
	
	require 'queries/pr_clear_host_usage.php';
	
	unlink($fulldir . '/' . $lockfile);
	
}

?>