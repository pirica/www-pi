<?php
set_time_limit(0);

require dirname(__FILE__).'/../_core/appinit.php';

require 'connection.php';
require 'functions.php';


if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	shell_exec('if [ ! -d /var/www/html/camera/captures ]; then ln -s ' . $main_dir . ' /var/www/html/camera/captures > /dev/null 2>&1; fi');
	shell_exec('if [ ! -d /var/www/html/camera/captures_archive ]; then ln -s ' . $archive_dir . ' /var/www/html/camera/captures_archive > /dev/null 2>&1; fi');
	shell_exec('if [ ! -d /var/www/html/camera/captures_thumbs ]; then ln -s ' . $thumbs_dir . ' /var/www/html/camera/captures_thumbs > /dev/null 2>&1; fi');
	//shell_exec('unlink /var/www/camera/captures');

	$crondate = time();

	shell_exec ('sudo chown nobody:nogroup -R "' . $main_dir . date('Ymd', time() - (60*60*24)) . '"');
	shell_exec ('sudo chmod 777 -R "' . $main_dir . date('Ymd', time() - (60*60*24)) . '"');

	shell_exec ('sudo chown nobody:nogroup -R "' . $main_dir . date('Ymd') . '"');
	shell_exec ('sudo chmod 777 -R "' . $main_dir . date('Ymd') . '"');
		
	$qry_camera_log_del = mysqli_query($conn, "
		
		select
			cl.date
			
		from t_camera_log cl
		where
			cl.date < date_format(now() - interval " . $settings->val('captures_days_kept', 30) . " day, '%Y%m%d')
		group by
			cl.date
			
			
		");
		
	while($logdel = mysqli_fetch_array($qry_camera_log_del)){
		shell_exec('rm -R ' . $main_dir . $logdel['date']);
		shell_exec('rm -R ' . $thumbs_dir . $logdel['date']);
	}

	mysqli_query($conn, "
		delete
		from t_camera_log
		where
			date < date_format(now() - interval " . $settings->val('captures_days_kept', 30) . " day, '%Y%m%d')
		");
		
	mysqli_query($conn, "
		delete
		from t_camera_menu
		where
			date < date_format(now() - interval " . $settings->val('captures_days_kept', 30) . " day, '%Y%m%d')
		");

	// clear complete table at 3 AM
	/*if(date("H", $crondate) == 3 && date("i", $crondate) < 10){
		mysqli_query($conn, "truncate table t_camera_log", $conn);
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
	*/
	$dircount = count($dirs);
	sort($dirs);


	//$files = [];
	$tmpfiles = [];
	//$filecount = 0;

	mysqli_query($conn, "
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
					if($tmpfiles[$i] != '' && strpos($tmpfiles[$i], '_') !== false && strpos($tmpfiles[$i], 'x0-y0-w0-h0') === false && filesize($main_dir . $dirs[$d] . '/' . $tmpfiles[$i]) > 0){
						$current_image++;
						
						// name = 20150911_150439_picam1_00_x572-y286-w8-h40.jpg
						// thumb = 20150911_150439_picam1.jpg
						$camera = explode('_', $tmpfiles[$i])[2];
						
						$hourstr = $tmpfiles[$i];
						$hourstr = explode('_', $hourstr)[1];
						$datestr = explode('_', $hourstr)[0];
						
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
								'".mysqli_real_escape_string($conn, $dirs[$d])."',
								'".mysqli_real_escape_string($conn, $hour_lbl)."',
								'".mysqli_real_escape_string($conn, $prev_time_lbl)."',
								".$timeval.",
								".$timeval_gif.",
								'".mysqli_real_escape_string($conn, $tmpfiles[$i])."',
								1,
								'".mysqli_real_escape_string($conn, $camera)."'
							)
							";
						
						if($i > 0 && $i % 100 == 0){
								
							mysqli_query($conn, "
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
								");
							
							$querydata = '';
						}
						
						// Generate thumbs
						if(!is_dir($thumbs_dir . $dirs[$d])){
							mkdir($thumbs_dir . $dirs[$d]);
						}
						
						$thumbWidth = 320; // setting
						$thumbnail = $thumbs_dir . $dirs[$d] . '/' . $dirs[$d] . '_' . $hours . $minutes . $seconds . '_' . $camera . '.jpg';

						if(!file_exists($thumbnail) && (stripos($tmpfiles[$i], '.jpg') > 0 || stripos($tmpfiles[$i], '.jpeg') > 0))
						{
							
							// load image and get image size
							$img = imagecreatefromjpeg($main_dir . $dirs[$d] . '/' . $tmpfiles[$i]);
							
							$width = imagesx( $img );
							$height = imagesy( $img );
							
							// calculate thumbnail size
							if($width > $height)
							{
								$new_width = $thumbWidth;
								$new_height = floor( $height * ( $thumbWidth / $width ) );
							}
							else 
							{
								$new_height = $thumbWidth;
								$new_width = floor( $width * ( $thumbWidth / $height ) );
							}
							
							// create a new temporary image
							$tmp_img = imagecreatetruecolor( $new_width, $new_height );

							// copy and resize old image into new image
							imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
							
							// save thumbnail into a file
							imagejpeg( $tmp_img, $thumbnail);
							
						}

						
					}
				}
				
				if($querydata != ''){
					mysqli_query($conn, "
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
						");
				}
				
			}
		}
	}

	mysqli_query($conn, "
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

		");


	mysqli_query($conn, "delete from t_camera_log where status = 2");
	mysqli_query($conn, "update t_camera_log set status = 0 where status = 1");

	shell_exec ('sudo chown nobody:nogroup -R "' . $thumbs_dir . date('Ymd') . '"');
	shell_exec ('sudo chmod 777 -R "' . $thumbs_dir . date('Ymd') . '"');
	
	
	$task->setIsRunning(false);
}

?>