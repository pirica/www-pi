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


// check if script is already running - no, continue
if($setting_fileindex_running == '0' && $setting_directoryindex_running == '0' && $setting_shareindex_running == '0'){
	// mark as running
	mysql_query("update t_setting set value = '1' where code = 'directoryindex_running'", $conn);
	

	// insert new (missing) directories
	mysql_query("truncate table t_directory_index ", $conn);

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
		echo "New share '" . $missing{'name'} . "' (" . $missing{'server_directory'} . ") added\n";
	}

	flush();

	$qry_shares = mysql_query("
		select
			s.id_share,
			s.name,
			s.info,
			s.server_directory,
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
			and s.external = 0
		order by s.id_share
		", $conn);
		

	$id_share = -1;
	
	$all_shares = '-1';

	while ($share = mysql_fetch_array($qry_shares)) {
		$all_shares .= ',' . $share{'id_share'};
		$id_share = $share{'id_share'};
		$dir = $share{'server_directory'};
		$date_last_replicated = $share{'date_last_replicated'};
		
		//echo 'Share '. $share{'name'} . "\n\n";
		
		$str = shell_exec('sudo /var/www/filerep/getdirectoryindex.sh "' . $dir . '"');
		
		//echo $str;
		
		$str = str_replace("\r", "\n", $str);
		$str = str_replace("\n\n", "\n", $str);
		$str = str_replace("\n\n", "\n", $str);
		$tmpdirs = explode("\n", $str);
		
		$tmpdircount = count($tmpdirs);
		$tmpinsertcount = 0;
		
		for ($i = 0; $i < $tmpdircount; $i++) {
			if($tmpdirs[$i] != ''){
				$dirname = $tmpdirs[$i];
				$dirname = str_replace($dir, '', $dirname); // remove main dir
				$dirname = substr($dirname, 0, -1);	// remove trailing ':'
				/*if($dirname == ''){
					$dirname = '/';
				}*/
				$dirname .= '/';
				
				mysql_query("
					insert into t_directory_index 
					(
						id_share,
						relative_directory
					)
					values
					(
						" . $id_share . ",
						'" . mysql_real_escape_string($dirname) . "'
					)
					
					", $conn);
			}
		}
		
		//echo '======================'. "\n\n\n\n";
		
	}

	// insert new ones
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
				when f.relative_directory = '/' then null
				else replace(replace(f.relative_directory, SUBSTRING_INDEX(f.relative_directory, '/', -2), ''), '//', '/')
			end as parent_directory,
			case 
				when f.relative_directory = '/' then ''
				else replace( SUBSTRING_INDEX(f.relative_directory, '/', -2), '/', '')
			end as dirname,
			1 as active,
			( ROUND (
				(
					LENGTH(f.relative_directory)
					- LENGTH( REPLACE ( f.relative_directory, '/', '') )
				) / LENGTH('/')
			) - 1 ) as depth

		from 
			t_directory_index f
			left join t_directory d on d.relative_directory = f.relative_directory
				and d.id_share = f.id_share 
				#and d.active = 1
				
		where
			d.id_directory is null
			and f.id_share in (".$all_shares.")
			
		group by
			f.id_share,
			f.relative_directory
			
		", $conn);
		
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
			and d.id_share in (".$all_shares.")
			
		", $conn);
	
	// remove deleted dir and flag to be reindexed (to delete files)
	mysql_query("
		
		update t_directory d
		join t_directory_index di on di.id_share = d.id_share
			and d.relative_directory = di.relative_directory
		set
			d.active = 1,
			d.date_last_checked = null
			
		where
			d.active = 0
			and d.id_share in (".$all_shares.")
			
		", $conn);
	
	/*
	// update dirname (take last part of directory structure)
	mysql_query("
		update t_directory 
		set
			dirname = replace( SUBSTRING_INDEX(relative_directory, '/', -2), '/', '')
		where
			ifnull(dirname,'') = ''
			and relative_directory <> ''
			and relative_directory <> '/'
		", $conn);
	*/
		
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
		
	// update parent directories 
	/*mysql_query("
		
		update t_directory d
		join t_directory td on td.id_share = d.id_share
			and d.relative_directory <> td.relative_directory
			and replace(replace(d.relative_directory, SUBSTRING_INDEX(d.relative_directory, '/', -2), ''), '//', '/') = td.relative_directory
		set
			d.id_directory_parent = td.id_directory
			
		where
			d.id_directory_parent is null
			and ifnull(d.id_directory_parent,-1) <> td.id_directory
			
		", $conn);*/
		
		
	// get max directory depth
	$qry_max_depth = mysql_query("
		select
			max(d.depth) as max_depth
		from t_directory d
		where
			d.parent_directory is not null
		", $conn);
	/*
	$qry_max_depth = mysql_query("
		select
			max( ROUND (
				(
					LENGTH(d.relative_directory)
					- LENGTH( REPLACE ( d.relative_directory, '/', '') )
				) / LENGTH('/')
			) - 1 ) as max_depth
		from t_directory d
		where
			d.parent_directory is not null
		", $conn);
	*/
	
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
	
	// script is done, unmark as running
	mysql_query("update t_setting set value = '0' where code = 'directoryindex_running'", $conn);
	
}

?>