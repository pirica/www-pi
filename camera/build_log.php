<?php
set_time_limit(0);

require 'connection.php';
require 'functions.php';

require dirname(__FILE__).'/../_core/appinit.php';

$crondate = time();

	
$qry_camera_log_del = mysql_query("
	
	select
		cl.id_camera_log,
		cl.date,
		cl.time,
		cl.hour_lbl,
		cl.time_value,
		cl.name
		
	from t_camera_log cl
	where
		cl.date < date_format(now() - interval " . $settings->val('captures_days_kept', 30) . " day, '%Y%m%d')
	order by
		cl.name
		
		
	");
	
while($logdel = mysql_fetch_array($qry_camera_log_del)){
	unlink($main_dir . $logdel['date'] . $logdel['name']);
	rmdir($main_dir . $logdel['date']);
}

// clear complete table at 3 AM
if(date("H", $crondate) == 3 && date("i", $crondate) < 10){
	mysql_query("truncate table t_camera_log", $conn);
}

$dirs = [];

$tmpdirs = str_replace($main_dir, '', shell_exec('find "' . $main_dir . '" -mindepth 1 -maxdepth 1'));
echo '<!--';
echo $tmpdirs;
echo '-->';
///*
$tmpdirs = str_replace("\r", "\n", $tmpdirs);
$tmpdirs = str_replace("\n\n", "\n", $tmpdirs);
$tmpdirs = str_replace("\n\n", "\n", $tmpdirs);
$dirs = explode("\n", $tmpdirs);
// */

$dircount = count($dirs);
sort($dirs);

$files = [];
$tmpfiles = [];
$filecount = 0;

for ($d = 0; $d < $dircount; $d++) {
	if($dirs[$d] != ''){
		$files[] = array(
			'name' => $dirs[$d],
			'subs' => [],
			'subcount' => 0
		);
		
		$tmpfilestr = str_replace($main_dir . $dirs[$d], '', shell_exec('find "' . $main_dir . $dirs[$d] . '" -mindepth 1 -maxdepth 1'));
		$tmpfilestr = str_replace("\r", "\n", $tmpfilestr);
		$tmpfilestr = str_replace("\n\n", "\n", $tmpfilestr);
		$tmpfilestr = str_replace("\n\n", "\n", $tmpfilestr);
		$tmpfiles = explode("\n", $tmpfilestr);
		
		$tmpfilecount = count($tmpfiles);
		
		$prev_time_lbl = '';
		
		if($tmpfilecount > 0){
			sort($tmpfiles);
			
			$querydata = '';
			
			for ($i = 0; $i < $tmpfilecount; $i++) {
				if($tmpfiles[$i] != '' && strpos($tmpfiles[$i], '_') !== false){
					// name = 20150211_220329-x190-y160-w256-h321_pibot.jpg
					$hourstr = $tmpfiles[$i];//['name'];
					$hourstr = explode('-', $hourstr)[0];
					$hourstr = explode('_', $hourstr)[1];
					
					$hours = substr($hourstr, 0, 2);
					$minutes = substr($hourstr, 2, 2);
					$seconds = substr($hourstr, 4, 2);
					
					$timeval = 0;
					$timeval += $seconds;
					$timeval += $minutes * 60;
					$timeval += $hours * 3600;
					
					if($settings->val('images_grouping_interval', 300) < 60){
						$hour_lbl = $hours . ':' . $minutes . ':' . $seconds;
					}
					else {
						$hour_lbl = $hours . ':' . $minutes;
					}
					
					if(count($files[count($files)-1]['subs']) == 0 || $files[count($files)-1]['subs'][count($files[count($files)-1]['subs'])-1]['timeval'] < $timeval - $settings->val('images_grouping_interval', 300)){
						$prev_time_lbl = $hour_lbl;
						
						$files[count($files)-1]['subs'][] = array(
							'hour_lbl' => $hour_lbl,
							'timeval' => $timeval,
							'files' => [],
							'filecount' => 0
						);
						$files[count($files)-1]['subcount']++;
						
					}
					else {
						$files[count($files)-1]['subs'][count($files[count($files)-1]['subs'])-1]['timeval'] = $timeval;
					}
					$files[count($files)-1]['subs'][count($files[count($files)-1]['subs'])-1]['files'][] = array(
						'name' => $tmpfiles[$i],
						'hour_lbl' => $hour_lbl,
						'timeval' => $timeval
					);
					$files[count($files)-1]['subs'][count($files[count($files)-1]['subs'])-1]['filecount']++;
					
					$querydata .= ($querydata == '' ? '' : ',');
					$querydata .= "
						(
							'".mysql_real_escape_string($dirs[$d])."',
							'".mysql_real_escape_string($hour_lbl)."',
							'".mysql_real_escape_string($prev_time_lbl)."',
							".$timeval.",
							'".mysql_real_escape_string($tmpfiles[$i])."',
							1
						)
						";
					
					if($i > 0 && $i % 100 == 0){
							
						mysql_query("
							insert into t_camera_log
							(
								date,
								time,
								hour_lbl,
								time_value,
								name,
								status
							)
							values
							" . $querydata . "
							", $conn);
						
						$querydata = '';
					}
				}
			}
			
			if($querydata != ''){
				mysql_query("
					insert into t_camera_log
					(
						date,
						time,
						hour_lbl,
						time_value,
						name,
						status
					)
					values
					" . $querydata . "
					", $conn);
			}
			
		}
	}
	$filecount = count($files);
}

mysql_query("delete from t_camera_log where ifnull(status,0) = 0", $conn);
mysql_query("update t_camera_log set status = 0", $conn);


?>