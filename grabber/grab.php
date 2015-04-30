<?
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connections.php';
include 'functions.php';



$qry_grabs = mysql_query("
	
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
		
	
	", $conn);
	
while ($grabs = mysql_fetch_array($qry_grabs)) {
	
	echo "Grab " . $grabs['description'] . " (ID:" .  $grabs['id_grab'] . ")<br>\n";
	echo " -> started on " . date('Y-m-d H:i:s', time()) . "<br>\n";
	
	// mark as 'running'
	mysql_query("update t_grab set running = 1 where id_grab = " . $grabs['id_grab'], $conn);
	
	
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
	
	mysql_query("update t_grab set diskspace_free = " . $pct_free . " where id_grab = " . $grabs['id_grab'], $conn);
	
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
		$qry_grab_files = mysql_query("
			
			select
				gf.id_grab_file,
				gf.full_url,
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
			
			", $conn);
		
		// completed
		if(mysql_num_rows($qry_grab_files) == 0){
			// if now completed (date = null)
			if($grabs['date_completed'] === NULL){
				// update when completed
				mysql_query("update t_grab set date_completed = now() where id_grab = " . $grabs['id_grab'], $conn);
				
				if($grabs['script_completion'] != ''){
					// and execute any scripts on completion
					$script_completion = $grabs['script_completion'];
					$script_completion = str_replace('%url%', $grabs['url'], $script_completion);
					$script_completion = str_replace('%path%', $grabs['path'], $script_completion);
					$script_completion = str_replace('%filename%', $grabs['filename'], $script_completion);
					$script_completion = str_replace('%description%', $grabs['description'], $script_completion);
					$script_completion = str_replace('%keep_diskspace_free%', $grabs['keep_diskspace_free'], $script_completion);
					$script_completion = str_replace('%always_retry%', $grabs['always_retry'], $script_completion);
					//$script_completion = str_replace('%%', $grabs[''], $script_completion);
				
					shell_exec($script_completion);
				}
			}
		}
		else {
			if($grabs['date_completed'] !== NULL){
				// clear completed
				mysql_query("update t_grab set date_completed = NULL where id_grab = " . $grabs['id_grab'], $conn);
			}
			
			while ($grabfile = mysql_fetch_array($qry_grab_files)) {

				$status = '';
				$status_info = '';
				
				// update in db  -- 'OK', 'NF', 'TO', 'FE', 'FX', E, P	-- ok, not found, timeout, file empty, file exists, error, processing
				mysql_query("
					
					update t_grab_file
					set
						status = 'P',
						status_info = '',
						date_modified = now()
						
					where
						id_grab_file = " . $grabfile['id_grab_file'] . "
					
				", $conn);
				
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
						
						$grabbedfile = cURLdownload($grabfile['full_url'], $grabfile['full_path']);
						
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
					mysql_query("
						
						update t_grab_file
						set
							status = '" . mysql_real_escape_string($status) . "',
							status_info = '" . mysql_real_escape_string($status_info) . "',
							date_modified = now(),
							filesize = " . $filesize . "
							
						where
							id_grab_file = " . $grabfile['id_grab_file'] . "
						
						", $conn);
					
				}
				else {
					echo "File exists: " . $grabfile['full_path'] . "<br>\n";
					
					// update in db  -- 'OK', 'NF', 'TO', 'FE', 'FX', E, P, X	-- ok, not found, timeout, file empty, file exists, error, processing, excluded
					mysql_query("
						
						update t_grab_file
						set
							status = 'FX',
							status_info = '',
							date_modified = now()
							
						where
							id_grab_file = " . $grabfile['id_grab_file'] . "
						
						", $conn);
					
				}
				
			}
		}
		
		// update grab stats
		include 'queries/pr_set_grab_stats.php';
		
	}
	
	// remove completed
	if($grabs['remove_completed_after_days'] > -1){
		mysql_query("
			update t_grab_file
			set
				active = 0,
				date_deleted = now()
			
			where
				id_grab = " . $grabs['id_grab'] . "
				and active = 1
				and ifnull(status,'') not in ('', 'N', 'P')
				and ifnull(date_modified, '1970-01-01') < (now() - interval " . $grabs['remove_completed_after_days'] . " day)
				
			", $conn);
	}
	
	// delete inactive
	if($grabs['remove_inactive_after_months'] > -1){
		mysql_query("
			delete from t_grab_file
			where
				id_grab = " . $grabs['id_grab'] . "
				and active = 0
				and ifnull(status,'') not in ('', 'N', 'P')
				and ifnull(date_deleted, '1970-01-01') < (now() - interval " . $grabs['remove_inactive_after_months'] . " month)
				
			", $conn);
	}
	
	// unmark as 'running'
	mysql_query("update t_grab set running = 0, date_last_run = now() where id_grab = " . $grabs['id_grab'], $conn);
	
	
	echo " -> completed on " . date('Y-m-d H:i:s', time()) . "<br>\n";
	echo "<br>\n";
	
	
}

?>