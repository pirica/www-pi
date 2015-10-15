<?php
set_time_limit(0);

require 'connection.php';
require 'functions.php';

require dirname(__FILE__).'/../_core/appinit.php';

$crondate = time();

shell_exec ('sudo chown nobody:nogroup -R "' . $main_dir . '"');
shell_exec ('sudo chmod 777 -R "' . $main_dir . '"');

	
$qry_camera_log_del = mysql_query("
	
	select
		cl.id_camera_log,
		cl.date,
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
mysql_query("
	delete
	from t_camera_log
	where
		date < date_format(now() - interval " . $settings->val('captures_days_kept', 30) . " day, '%Y%m%d')
	;
	delete
	from t_camera_menu
	where
		date < date_format(now() - interval " . $settings->val('captures_days_kept', 30) . " day, '%Y%m%d')
	;
	");

// clear complete table at 3 AM
/*if(date("H", $crondate) == 3 && date("i", $crondate) < 10){
	mysql_query("truncate table t_camera_log", $conn);
}*/

$dirs = [date('Ymd', time() - (60*60*24)), date('Ymd')];
//$tmpdirs .= str_replace($main_dir, '', shell_exec('find "' . $main_dir . '" -mindepth 1 -maxdepth 1'));
$tmpdirs = '';

/*
$datedir = date('Ymd', time() - (60*60*24));
$tmpdirs .= str_replace($main_dir, '', shell_exec('find "' . $main_dir . $datedir . '/' . '" -mindepth 1 -maxdepth 1'));

$tmpdirs .= "\r\n";

$datedir = date('Ymd');
$tmpdirs .= str_replace($main_dir, '', shell_exec('find "' . $main_dir . $datedir . '/' . '" -mindepth 1 -maxdepth 1'));

$tmpdirs .= "\r\n";

echo '<!--';
echo $tmpdirs;
echo '-->';

$dircount = count($dirs);
sort($dirs);
*/

//$files = [];
$tmpfiles = [];
//$filecount = 0;

mysql_query("
	update t_camera_log
	set status = 2
	where
		date >= date_format(now() - interval 1 day, '%Y%m%d')
	
	");

for ($d = 0; $d < $dircount; $d++) {
	if($dirs[$d] != ''){
		
		$tmpfilestr = str_replace($main_dir . $dirs[$d], '', shell_exec('find "' . $main_dir . $dirs[$d] . '" -mindepth 1 -maxdepth 1'));
		$tmpfilestr = str_replace("\r", "\n", $tmpfilestr);
		$tmpfilestr = str_replace("\n\n", "\n", $tmpfilestr);
		$tmpfilestr = str_replace("\n\n", "\n", $tmpfilestr);
		$tmpfiles = explode("\n", $tmpfilestr);
		
		$tmpfilecount = count($tmpfiles);
		
		$prev_time_lbl = '';
		$prev_timeval = -9999;
		$prev_timeval_gif = -9999;
		$timeval_gif = -9999;
		
		$current_image = 0;
		
		
		if($tmpfilecount > 0){
			sort($tmpfiles);
			
			$querydata = '';
			
			for ($i = 0; $i < $tmpfilecount; $i++) {
				if($tmpfiles[$i] != '' && strpos($tmpfiles[$i], '_') !== false && strpos($tmpfiles[$i], 'x0-y0-w0-h0') === false){
					$current_image++;
					
					// name = 20150911_150439_picam1_00_x572-y286-w8-h40.jpg
					$camera = explode('_', $tmpfiles[$i])[2];
					
					$hourstr = $tmpfiles[$i];
					$hourstr = explode('_', $hourstr)[1];
					
					$hours = substr($hourstr, 0, 2);
					$minutes = substr($hourstr, 2, 2);
					$seconds = substr($hourstr, 4, 2);
					
					$timeval = 0;
					$timeval += $seconds;
					$timeval += $minutes * 60;
					$timeval += $hours * 3600;
					
					//if($settings->val('images_grouping_interval', 300) < 60){
						$hour_lbl = $hours . ':' . $minutes . ':' . $seconds;
					/*}
					else {
						$hour_lbl = $hours . ':' . $minutes;
					}*/
					
					if($prev_timeval < $timeval - $settings->val('images_grouping_interval', 300) || $current_image >= $settings->val('max_images_per_grouping', 1000)){
						$prev_time_lbl = $hour_lbl;
						$current_image = 0;
					}
					$prev_timeval = $timeval;
					
					if($prev_timeval_gif + 1 < $timeval){
						$timeval_gif = $timeval;
					}
					$prev_timeval_gif = $timeval;
					
					
					$querydata .= ($querydata == '' ? '' : ',');
					$querydata .= "
						(
							'".mysql_real_escape_string($dirs[$d])."',
							'".mysql_real_escape_string($hour_lbl)."',
							'".mysql_real_escape_string($prev_time_lbl)."',
							".$timeval.",
							".$timeval_gif.",
							'".mysql_real_escape_string($tmpfiles[$i])."',
							1,
							'".mysql_real_escape_string($camera)."'
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
								time_value_gif,
								name,
								status,
								camera
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
						time_value_gif,
						name,
						status,
						camera
					)
					values
					" . $querydata . "
					", $conn);
			}
			
		}
	}
}

mysql_query("
	replace into t_camera_menu
	(
		date_hour_lbl,
		date,
		hour_lbl,
		nbr_images,
		nbr_videos
	)
	select
		concat(cl.date, '-', cl.hour_lbl) date_hour_lbl,
		cl.date,
		cl.hour_lbl,
		sum(case when cl.name like '%.jpg' then 1 else 0 end) as nbr_images,
		sum(case when cl.name like '%.mp4' or cl.name like '%.avi' then 1 else 0 end) as nbr_videos
		
	from t_camera_log cl
	where
		ifnull(status,0) = 1
	
	group by
		cl.date,
		cl.hour_lbl
		
	order by
		cl.date,
		cl.hour_lbl

	", $conn);


mysql_query("delete from t_camera_log where status = 2", $conn);
mysql_query("update t_camera_log set status = 0 where status = 1", $conn);


?>