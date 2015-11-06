<?php
/**
 *	Build directory tree
 *	
 */
 
set_time_limit(0);
ini_set('max_input_time', 99999);

include 'connection.php';
include 'act_settings.php';
include 'functions.php';

function curl_get_contents($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

// check if script is already running - no, continue
//if($setting_fileindex_running == '0' && $setting_directoryindex_running == '0' && $setting_shareindex_running == '0'){
	// mark as running
	//mysql_query("update t_setting set value = '1' where code = 'directoryindex_running'", $conn);
	

	// insert new (missing) directories
	//mysql_query("truncate table t_directory_index ", $conn);
	
	// insert base dirs (c:, d:)
	mysql_query("
		insert into t_external_index
		(
			id_share,
			filename,
			relative_directory,
			is_dir,
			size,
			modified,
			do_check
		) 
		select
			s.id_share,
			'c:' as filename,
			'/c:/' as relative_directory,
			1 as is_dir,
			-1 as size,
			null as modified,
			1 as do_check

		from 
			t_share s
			left join t_external_index d on d.relative_directory = 'c:/'
				and d.id_share = s.id_share 
				#and d.active = 1
				
		where
			s.external = 1
			and d.id_external_index is null
			
		", $conn);
		
	// insert new (missing) links between shares and server host
	$qry_missing_str = "
		from 
			t_host h
			join t_share s on 1=1
			left join t_host_share hs on hs.id_host = h.id_host and hs.id_share = s.id_share 
		where
			h.id_host = " . $setting_server_id_host . " 
			and s.active = 1
			and hs.id_host_share is null
		";
	$qry_missing = mysql_query("select h.id_host, s.id_share, s.server_directory, s.name " . $qry_missing_str, $conn);
	$qry_insert_missing = mysql_query("
		insert into t_host_share (id_host, id_share, local_directory) 
		select h.id_host, s.id_share, s.server_directory " .
		$qry_missing_str
		, $conn);
		
	while ($missing = mysql_fetch_array($qry_missing)) {
		echo "New share '" . $missing{'name'} . "' (" . $missing{'server_url'} . ") added\n";
	}

	flush();

	$qry_shares = mysql_query("
		select
			s.id_share,
			s.name,
			s.info,
			s.server_url,
			hs.id_host_share,
			hs.id_host,
			hs.local_directory,
			hs.date_linked_since,
			hs.date_last_replicated
		from t_share s
		join t_host_share hs on hs.id_share = s.id_share
			and hs.active = 1
			and hs.id_host = " . $setting_server_id_host . " 
		where
			s.active = 1
			and s.external = 1
		order by s.id_share
		", $conn);
		

	$id_share = -1;

	while ($share = mysql_fetch_array($qry_shares)) {
		$id_share = $share{'id_share'};
		$server_url = $share{'server_url'};
		$date_last_replicated = $share{'date_last_replicated'};
		
		$online = 1;
		$check = 'checking...';
		try {
			$check = curl_get_contents($server_url . '/get.php');
			echo $check;
			if(strpos($check, '====') !== false){
				$online = 1;
			}
		}
		catch(Exception $e){
		}
		
		echo 'Share '. $share{'name'} . ": " . ($online == 1 ? 'online' : 'offline') . "" . "<br>\r\n";
		
		if($online == 1){
			
			$qry_dirs = mysql_query("
				select
					ei.id_external_index,
					ei.id_share,
					ei.filename,
					ei.relative_directory,
					hs.id_host_share,
					hs.id_host,
					hs.local_directory,
					hs.date_linked_since,
					hs.date_last_replicated
				from t_external_index ei
				join t_host_share hs on hs.id_share = ei.id_share
					and hs.active = 1
					and hs.id_host = " . $setting_server_id_host . " 
				where
					ei.id_share = " . $id_share . "
					and ei.is_dir = 1
					and ei.do_check = 1
				limit 20
				
				", $conn);
			
			while ($dirs = mysql_fetch_array($qry_dirs)) {
				$relative_directory = $dirs['relative_directory'];
				if(substr($relative_directory, 0, 1) == '/'){
					$relative_directory = substr($relative_directory, 1);
				}
					
				$raw = file_get_contents($server_url . '/dir.php?d=' . urlencode($relative_directory));
				
				echo $raw . "<br>\r\n";
				
				$raw = json_decode($raw);
				
				//echo $raw->logging;
				
				if(isset($raw->data)){
					$tmpdirs = $raw->data;
					
					$tmpdircount = count($tmpdirs);
					$tmpinsertcount = 0;
					
					for ($i = 0; $i < $tmpdircount; $i++) {
						if($tmpdirs[$i] != ''){
							if($tmpdirs[$i]->modified != ''){
								$modified = "'" . date('Y-m-d H:i:s', $tmpdirs[$i]->modified) . "'";
							}
							else {
								$modified = "NULL";
							}
							
							$relative_directory = $dirs['relative_directory'];
							if(substr($relative_directory, 0, 1) == '/'){
								$relative_directory = substr($relative_directory, 1);
							}
							
							mysql_query("
								insert into t_external_index
								(
									id_share,
									filename,
									relative_directory,
									is_dir,
									size,
									modified,
									do_check
								) 
								values
								(
									" . $id_share . ",
									'" . mysql_real_escape_string($tmpdirs[$i]->filename) . "',
									'/" . mysql_real_escape_string($dirs['relative_directory'] . ($tmpdirs[$i]->dir == 1 ? $tmpdirs[$i]->filename . '/' : '') ) . "',
									" . $tmpdirs[$i]->dir . ",
									" . $tmpdirs[$i]->size . ",
									" . $modified . ",
									1
								)
								
								", $conn);
						}
					}
					
					mysql_query("
						update t_external_index
						set do_check = 0
						where
							id_external_index = " . $dirs['id_external_index'] . "
							
						", $conn);
				}
			}
			
			//echo '======================<br>'. "\r\n\r\n";
		}
	}
	

	// insert root
	mysql_query("
		insert into t_directory
		(
			id_share,
			relative_directory,
			parent_directory,
			dirname,
			active,
			depth
		) 
		select
			f.id_share,
			'/' as relative_directory,
			null as parent_directory,
			'' as dirname,
			1 as active,
			0 as depth

		from 
			t_share f
			left join t_directory d on d.relative_directory = '/'
				and d.id_share = f.id_share 
				#and d.active = 1
				
		where
			f.id_share = " . $id_share . "
			and d.id_directory is null
			
		group by
			f.id_share,
			f.relative_directory
			
		", $conn);
		
	// insert new directories
	mysql_query("
		insert into t_directory
		(
			id_share,
			relative_directory,
			parent_directory,
			dirname,
			active,
			depth
		) 
		select
			f.id_share,
			f.relative_directory as relative_directory,
			case 
				when f.relative_directory = '/c:/' then '/'
				else replace(replace(f.relative_directory, SUBSTRING_INDEX(f.relative_directory, '/', -2), ''), '//', '/')
			end as parent_directory,
			#case 
			#	when f.relative_directory = '/c:/' then 'c:'
			#	else replace( SUBSTRING_INDEX(f.relative_directory, '/', -2), '/', '')
			#end as dirname,
			f.filename,
			1 as active,
			( ROUND (
				(
					LENGTH(f.relative_directory)
					- LENGTH( REPLACE ( f.relative_directory, '/', '') )
				) / LENGTH('/')
			) - 1 ) as depth

		from 
			t_external_index f
			left join t_directory d on d.relative_directory = f.relative_directory
				and d.id_share = f.id_share 
				#and d.active = 1
				
		where
			f.is_dir = 1
			and d.id_directory is null
			
		group by
			f.id_share,
			f.relative_directory
			
		", $conn);
	
	// insert new files
	mysql_query("
		insert into t_file
		(
			id_share,
			filename,
			relative_directory,
			size,
			date_last_modified,
			active
		) 
		select
			f.id_share,
			f.filename,
			f.relative_directory,
			f.size,
			f.modified,
			1 as active

		from 
			t_external_index f
			left join t_file d on d.relative_directory = f.relative_directory
				and d.filename = f.filename
				and d.id_share = f.id_share 
				#and d.active = 1
		where
			f.is_dir = 0
			and d.id_file is null
			
		group by
			f.id_share,
			f.relative_directory

		", $conn);

/*
	// remove deleted dir and flag to be reindexed (to delete files)
	mysql_query("
		
		update t_directory d
		left join t_directory_index di on di.id_share = d.id_share
			and d.relative_directory = di.relative_directory
		set
			d.active = 0,
			d.date_deleted = now(),
			d.date_last_checked = null
			
		where
			d.active = 1
			and di.id_directory_index is null
			
		", $conn);
	
		
	// update directories
	mysql_query("
		update t_directory d
		join (
			select
				f.id_share,
				f.relative_directory,
				sum(f.size) as size,
				max(f.date_last_modified) as date_last_modified,
				sum(case when f.active = 1 then 1 else 0 end) as nbr_files,
				sum(case when f.active = 0 then 1 else 0 end) as nbr_files_inactive
			from 
				t_file f
			group by
				f.id_share,
				f.relative_directory
				
		) f on f.id_share = d.id_share
			and d.relative_directory = f.relative_directory
		set
			#d.active = f.active,
			d.size = f.size,
			d.date_last_modified = f.date_last_modified,
			d.nbr_files = f.nbr_files,
			d.nbr_files_inactive = f.nbr_files_inactive
		
		", $conn);
	
		
	// get max directory depth
	$qry_max_depth = mysql_query("
		select
			max(d.depth) as max_depth
		from t_directory d
		where
			d.parent_directory is not null
		", $conn);
	
	
	$max_depth = mysql_fetch_array($qry_max_depth)['max_depth'];

	while($max_depth > 0){
		// update directory stats (size, dates)
		mysql_query("
			
			update t_directory d
			join (
				select
					dt.parent_directory,
					dt.id_share,
					sum(ifnull(dt.size,0) + ifnull(dt.size_sub,0)) as size,
					max(dt.active) as active,
					max(dt.date_last_modified) as date_last_modified,
					sum(ifnull(dt.nbr_files,0) + ifnull(dt.nbr_files_sub,0)) as nbr_files,
					sum(ifnull(dt.nbr_files_inactive,0) + ifnull(dt.nbr_files_inactive_sub,0)) as nbr_files_inactive
				from t_directory dt
				where
					#ROUND (
					#	(
					#		LENGTH(dt.relative_directory)
					#		- LENGTH( REPLACE ( dt.relative_directory, '/', '') )
					#	) / LENGTH('/')
					#) - 1 = " . $max_depth . "
					dt.depth = " . $max_depth . "
				group by 
					dt.parent_directory,
					dt.id_share
				
				
			) dp  on dp.parent_directory = d.relative_directory and dp.id_share = d.id_share
			set
				d.size_sub = dp.size,
				
				#d.active = dp.active,
				d.date_last_modified = dp.date_last_modified,
				
				d.nbr_files_sub = dp.nbr_files,
				d.nbr_files_inactive_sub = dp.nbr_files_inactive
			
			", $conn);
			
		$max_depth--;
	}
	
*/

	// script is done, unmark as running
	//mysql_query("update t_setting set value = '0' where code = 'directoryindex_running'", $conn);
	
//}

?>