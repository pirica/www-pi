<?
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connections.php';
include 'functions.php';



$qry_grabs = mysqli_query($conn, "
	
	select
		g.id_grab,
		g.url,
		g.path,
		g.filename,
		g.description,
		ifnull(g.script_completion,'') as script_completion,
		ifnull(remove_completed_after_days,-1) as remove_completed_after_days,
		ifnull(remove_inactive_after_months,-1) as remove_inactive_after_months,
		ifnull(g.max_grabbers, " . $settings->val('grabber_maxgrabbers_default', 20) . ") as max_grabbers,
		ifnull(g.excluded,'') as excluded,
		ifnull(g.excluded_size,0) as excluded_size,
		ifnull(g.keep_diskspace_free, " . $settings->val('keep_diskspace_free_default', 5) . ") as keep_diskspace_free,
		ifnull(g.always_retry,0) as always_retry,
		g.date_completed
	from t_grab g
	where
		g.active = 1
		and g.enabled = 1
		and g.scheduled = 0
		and 
		(
			ifnull(g.running, 0) = 0
			or
			(ifnull(g.running, 0) = 1 and ifnull(date_last_run, '1970-01-01') < now() - interval " . $settings->val('interval_retry_running_minutes', 60) . " minute)
		)
		
	union
	
	select
		g.id_grab,
		g.url,
		g.path,
		g.filename,
		g.description,
		ifnull(g.script_completion,'') as script_completion,
		ifnull(remove_completed_after_days,-1) as remove_completed_after_days,
		ifnull(remove_inactive_after_months,-1) as remove_inactive_after_months,
		ifnull(g.max_grabbers, " . $settings->val('grabber_maxgrabbers_default', 20) . ") as max_grabbers,
		ifnull(g.excluded,'') as excluded,
		ifnull(g.excluded_size,0) as excluded_size,
		ifnull(g.keep_diskspace_free, " . $settings->val('keep_diskspace_free_default', 5) . ") as keep_diskspace_free,
		ifnull(g.always_retry,0) as always_retry,
		g.date_completed
	from t_grab g
	join t_grab_schedule gs on gs.id_grab = g.id_grab and gs.active = 1
		
		# 0 = sunday, 6 = saturday
		and ifnull(gs.weekday_from, cast(DATE_FORMAT(now(), '%w') as unsigned) ) <= cast(DATE_FORMAT(now(), '%w')  as unsigned)
		and ifnull(gs.weekday_to, cast(DATE_FORMAT(now(), '%w') as unsigned) ) >= cast(DATE_FORMAT(now(), '%w')  as unsigned)
		
		and ifnull(gs.day_from, cast(DATE_FORMAT(now(), '%d') as unsigned) ) <= cast(DATE_FORMAT(now(), '%d')  as unsigned)
		and ifnull(gs.day_to, cast(DATE_FORMAT(now(), '%d') as unsigned) ) >= cast(DATE_FORMAT(now(), '%d')  as unsigned)
		
		and ifnull(gs.month_from, cast(DATE_FORMAT(now(), '%m') as unsigned) ) <= cast(DATE_FORMAT(now(), '%m')  as unsigned)
		and ifnull(gs.month_to, cast(DATE_FORMAT(now(), '%m') as unsigned) ) >= cast(DATE_FORMAT(now(), '%m')  as unsigned)
		
		and ifnull(gs.hour_from, cast(DATE_FORMAT(now(), '%H') as unsigned) ) <= cast(DATE_FORMAT(now(), '%H')  as unsigned)
		and ifnull(gs.hour_to, cast(DATE_FORMAT(now(), '%H') as unsigned) ) >= cast(DATE_FORMAT(now(), '%H')  as unsigned)
		
		and ifnull(gs.minute_from, cast(DATE_FORMAT(now(), '%i') as unsigned) ) <= cast(DATE_FORMAT(now(), '%i')  as unsigned)
		and ifnull(gs.minute_to, cast(DATE_FORMAT(now(), '%i') as unsigned) ) >= cast(DATE_FORMAT(now(), '%i')  as unsigned)
		
	where
		g.active = 1
		and g.enabled = 1
		and g.scheduled = 1
		and 
		(
			ifnull(g.running, 0) = 0
			or
			(ifnull(g.running, 0) = 1 and ifnull(date_last_run, '1970-01-01') < now() - interval " . $settings->val('interval_retry_running_minutes', 60) . " minute)
		)
		
	
	");
	
while ($grabs = mysqli_fetch_array($qry_grabs)) {
	
	echo "Grab " . $grabs['description'] . " (ID:" .  $grabs['id_grab'] . ")<br>\n";
	echo " -> started on " . date('Y-m-d H:i:s', time()) . "<br>\n";
	
	// mark as 'running'
	mysqli_query($conn, "update t_grab set running = 1 where id_grab = " . $grabs['id_grab']);
	
	
	$disk_free_space = @disk_free_space($grabs['path']);
	$disk_total_space = @disk_total_space($grabs['path']);
	
	// could not check disk space (false => 0)
	if($disk_free_space === false){
		$disk_free_space = 0;
	}
	if($disk_total_space === false){
		$disk_total_space = 0;
	}
	
	$pct_free = -1;
	if($disk_total_space > 0){
		$pct_free = $disk_free_space / $disk_total_space * 100;
	}
	
	mysqli_query($conn, "update t_grab set diskspace_free = " . $pct_free . " where id_grab = " . $grabs['id_grab']);
	
	// not enough disk space
	if($disk_total_space > 0 && $pct_free <= $grabs['keep_diskspace_free']){
		echo " -> not enough free disk space for " . $grabs['path'] . ", % free = " . round($pct_free,1) . "<br>\n";
	}
	
	// enough disk space or could not check
	else {
		
		// could not check disk space
		if($disk_total_space == 0){
			echo " -> could not check free disk space for " . $grabs['path'] . "<br>\n";
		}
		
		// get batch of files
		$qry_grab_files = mysqli_query($conn, "
			
			select
				gf.id_grab_file,
				gf.full_url,
				gf.referer,
				gf.type,
				
				gf.full_path,
				
				gf.status,
				gf.status_info
				
			from t_grab_file gf
			where
				gf.id_grab = " . $grabs['id_grab'] . "
				and gf.active = 1
				and (
					ifnull(gf.status,'') in ('', 'N' " . ($grabs['always_retry'] == 1 ? ", 'FE', 'TO', 'E'" : '') . ")
					or
					(ifnull(gf.status,'') in ('P') and ifnull(gf.date_modified, '1970-01-01') < now() - interval " . $settings->val('interval_retry_files_days', 60) . " day)
				)
				
		#	order by
				#" . ($grabs['always_retry'] == 1 ? 'ifnull(gf.date_modified,gf.date_inserted),' : '' ) ."
				#gf.id_grab_file
		#		ifnull(gf.date_modified,gf.date_inserted)
			
			limit 0, " . $grabs['max_grabbers'] . "
			
			");
		
		// completed
		if(mysqli_num_rows($qry_grab_files) == 0){
			// if now completed (date = null)
			if($grabs['date_completed'] === NULL){
				// update when completed
				mysqli_query("update t_grab set date_completed = now() where id_grab = " . $grabs['id_grab'], $conn);
				
				if($grabs['script_completion'] != ''){
					// and execute any scripts on completion
					$script_completion = $grabs['script_completion'];
					$script_completion = str_replace('%url%', $grabs['url'], $script_completion);
					$script_completion = str_replace('%full_url%', $grabs['full_url'], $script_completion);
					$script_completion = str_replace('%full_path%', $grabs['full_path'], $script_completion);
					$script_completion = str_replace('%path%', $grabs['path'], $script_completion);
					$script_completion = str_replace('%filename%', $grabs['filename'], $script_completion);
					$script_completion = str_replace('%description%', $grabs['description'], $script_completion);
					$script_completion = str_replace('%keep_diskspace_free%', $grabs['keep_diskspace_free'], $script_completion);
					$script_completion = str_replace('%always_retry%', $grabs['always_retry'], $script_completion);
					//$script_completion = str_replace('%%', $grabs[''], $script_completion);
				
					$filename_part = $grabs['full_path'];
					$script_completion = str_replace('%filename_part%', $filename_part, $script_completion);
					
					shell_exec($script_completion);
				}
			}
		}
		else {
			if($grabs['date_completed'] !== NULL){
				// clear completed
				mysqli_query($conn, "update t_grab set date_completed = NULL where id_grab = " . $grabs['id_grab']);
			}
			
			while ($grabfile = mysqli_fetch_array($qry_grab_files)) {

				$status = '';
				$status_info = '';
				
				// update in db  -- 'OK', 'NF', 'TO', 'FE', 'FX', E, P	-- ok, not found, timeout, file empty, file exists, error, processing
				mysqli_query($conn, "
					
					update t_grab_file
					set
						status = 'P',
						status_info = '',
						date_modified = now()
						
					where
						id_grab_file = " . $grabfile['id_grab_file'] . "
					
				");
				
				// check if file exists - don't need to download twice
				if(!file_exists($grabfile['full_path'])){
					
					// check if dir exists: /dir/subdir/file.ext => /dir/subdir/
					$dirname = explode('/', $grabfile['full_path']);
					array_pop($dirname);
					$dirname = implode('/', $dirname);
					if(!is_dir($dirname)){
						mkdir($dirname, 777, true); // dirname, mode, recursive
						$status_info = $status_info . 'Directory created: ' . $dirname . '<br><br>';
						
						echo "Directory created: " . $dirname. "<br>\n";
						
					}
					
					$filesize = 0;
					
					// grab file 
					try {
					
						switch($grabfile['type']){
							case 'youtube-dl':
								if(strpos($grabfile['full_path'], '.mp3') > 0){
									$grabbedfile = shell_exec('/usr/local/bin/youtube-dl --no-part --extract-audio --audio-format mp3 --audio-quality 0 --prefer-avconv -o "' . str_replace('.mp3', '.%(ext)s', $grabfile['full_path']) . '" ' . $grabfile['full_url']);
								}
								else {
									$grabbedfile = shell_exec('/usr/local/bin/youtube-dl --no-part -o "' . $grabfile['full_path'] . '" ' . $grabfile['full_url']);
								}
								
								/* // error
[youtube] y_lhqg_p21k: Downloading webpage
[youtube] y_lhqg_p21k: Downloading video info webpage
[youtube] y_lhqg_p21k: Extracting video information
[youtube] y_lhqg_p21k: Downloading DASH manifest
[youtube] y_lhqg_p21k: Downloading DASH manifest
WARNING: Your copy of avconv is outdated and unable to properly mux separate video and audio files, youtube-dl will download single file media. Update avconv to version 10-0 or newer to fix this.
[download] Destination: /var/docs/downloads/Mythbusting Linux..mp4
[download]  58.5% of 199.07MiB at  3.39MiB/s ETA 00:24ERROR: unable to download video data: [Errno 1] _ssl.c:1415: error:1408F119:SSL routines:SSL3_GET_RECORD:decryption failed or bad record mac
								*/
								/* // success
[youtube] y_lhqg_p21k: Downloading webpage
[youtube] y_lhqg_p21k: Downloading video info webpage
[youtube] y_lhqg_p21k: Extracting video information
[youtube] y_lhqg_p21k: Downloading DASH manifest
[youtube] y_lhqg_p21k: Downloading DASH manifest
WARNING: Your copy of avconv is outdated and unable to properly mux separate video and audio files, youtube-dl will download single file media. Update avconv to version 10-0 or newer to fix this.
[download] Destination: /var/docs/downloads/Mythbusting Linux..mp4
[download] 100% of 199.07MiB in 00:58
								*/
								break;
								
							default:
								$grabbedfile = cURLdownload($grabfile['full_url'], $grabfile['full_path'], 5, $grabfile['referer']);
								
						}
						
						if(isset($grabbedfile)){
							
							$filesize = filesize($grabfile['full_path']);
							
							if($filesize == 0){
								$status = 'FE';
								$status_info = $status_info . $grabbedfile;
								echo "File empty: " . $grabbedfile . "<br>\n";
								unlink($grabfile['full_path']); // delete
							}
							else if($grabs['excluded_size'] * 1024 > $filesize){
								$status = 'X';
								$status_info = $status_info . $grabbedfile;
								$status_info = $status_info . '<br>' . "File excluded by size: " . $grabs['excluded_size'];
								echo "File excluded by size: " . $grabbedfile . "<br>\n";
								unlink($grabfile['full_path']); // delete
							}
							else if($grabs['excluded'] != '' && stripos(file_get_contents($grabfile['full_path']), $grabs['excluded']) !== false){
								$status = 'X';
								$status_info = $status_info . $grabbedfile;
								$status_info = $status_info . '<br>' . "File excluded by content: " . $grabs['excluded'];
								echo "File excluded by content: " . $grabbedfile . "<br>\n";
								unlink($grabfile['full_path']); // delete
							}
							else if($grabfile['type'] == 'youtube-dl' && strpos($grabbedfile, "error") === false){
								$status = 'OK';
								$status_info = $status_info . $grabbedfile;
								echo "File OK: " . $grabbedfile . "<br>\n";
							}
							else if(strpos($grabbedfile, "SUCCESS") == 0){
								$status = 'OK';
								$status_info = $status_info . $grabbedfile;
								echo "File OK: " . $grabbedfile . "<br>\n";
							}
							else {
								$status = 'E';
								$status_info = $status_info . $grabbedfile;
								echo "File error: " . $grabbedfile . "<br>\n";
							}
							
						}
						else {
							$status = 'E';
							$status_info = $status_info . '<br>grabbedfile undefined!';
							echo "grabbedfile undefined!<br>\n";
						}
						
					}
					catch(Exception $e){
						echo "Error: " . $e->getMessage() . "<br>\n";
						$status = 'E';
						if(isset($grabbedfile)){
							$status_info = $status_info . $grabbedfile;
							echo $grabbedfile . "<br>\n";
						}
						$status_info = $status_info . $e->getMessage();
						
					}
					
					// update in db  -- 'OK', 'NF', 'TO', 'FE', 'FX', E, P, X	-- ok, not found, timeout, file empty, file exists, error, processing, excluded
					mysqli_query($conn, "
						
						update t_grab_file
						set
							status = '" . mysqli_real_escape_string($conn, $status) . "',
							status_info = '" . mysqli_real_escape_string($conn, $status_info) . "',
							date_modified = now(),
							filesize = " . $filesize . "
							
						where
							id_grab_file = " . $grabfile['id_grab_file'] . "
						
						");
					
				}
				else {
					echo "File exists: " . $grabfile['full_path'] . "<br>\n";
					
					// update in db  -- 'OK', 'NF', 'TO', 'FE', 'FX', E, P, X	-- ok, not found, timeout, file empty, file exists, error, processing, excluded
					mysqli_query($conn, "
						
						update t_grab_file
						set
							status = 'FX',
							status_info = '',
							date_modified = now()
							
						where
							id_grab_file = " . $grabfile['id_grab_file'] . "
						
						");
					
				}
				
			}
		}
		
		// update grab stats
		include 'queries/pr_set_grab_stats.php';
		
	}
	
	// remove completed
	if($grabs['remove_completed_after_days'] > -1){
		mysqli_query($conn, "
			update t_grab_file
			set
				active = 0,
				date_deleted = now()
			
			where
				id_grab = " . $grabs['id_grab'] . "
				and active = 1
				and ifnull(status,'') not in ('', 'N', 'P')
				and ifnull(date_modified, '1970-01-01') < (now() - interval " . $grabs['remove_completed_after_days'] . " day)
				
			");
	}
	
	// delete inactive
	if($grabs['remove_inactive_after_months'] > -1){
		mysqli_query($conn, "
			delete from t_grab_file
			where
				id_grab = " . $grabs['id_grab'] . "
				and active = 0
				and ifnull(status,'') not in ('', 'N', 'P')
				and ifnull(date_deleted, '1970-01-01') < (now() - interval " . $grabs['remove_inactive_after_months'] . " month)
				
			");
	}
	
	// unmark as 'running'
	mysqli_query($conn, "update t_grab set running = 0, date_last_run = now() where id_grab = " . $grabs['id_grab']);
	
	
	echo " -> completed on " . date('Y-m-d H:i:s', time()) . "<br>\n";
	echo "<br>\n";
	
	
}

?>