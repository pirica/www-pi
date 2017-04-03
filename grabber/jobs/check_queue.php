<?php
set_time_limit(3600);
require dirname(__FILE__).'/../connections.php';
require dirname(__FILE__).'/../functions.php';
require dirname(__FILE__).'/../../_core/appinit.php';

/*
Queue statuses:
	N: new
	Y: is youtube-dl downloadable
	F: download as regular file
	V: download with youtube-dl
	D: download normally
	A: added to downloads
	X: ignored/excluded/deleted
*/

if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	$qry_queue = mysqli_query($conn, "
		
		select
			q.id_queue,
			q.url
			
		from t_queue q
		where
			q.status = 'N'
			and q.filename = ''
			
		");

	while ($queue = mysqli_fetch_array($qry_queue)) {
		
		$ytdl = shell_exec('/usr/bin/youtube-dl --get-filename ' . $queue['url']);
		$filename = '';
		
		// youtube-dl found a file, update
		if(isset($ytdl) && $ytdl != ''){
			$type = 'youtube-dl';
			$filename = $ytdl;
			
			mysqli_query($conn, "
				update t_queue
				set
					filename = '" . mysqli_real_escape_string($conn, $filename) . "',
					status = 'Y'
				where
					id_queue = " . $queue['id_queue'] . "
				");
			
		}
		// youtube-dl couldn't load, check mime type of url
		else
		{
			$filename_a = explode('/', $queue['url']);
			$filename = $filename_a[count($filename_a) - 1];
			
			$filename = str_replace("\r", ' ', str_replace("\n", ' ', str_replace("\t", ' ', $filename)));
			$filename = str_replace("+", ' ', $filename);
			
			$filename = str_replace("/", '-', $filename);
			$filename = str_replace(":", '-', $filename);
			$filename = str_replace("*", '-', $filename);
			$filename = str_replace("?", '-', $filename);
			$filename = str_replace('"', '-', $filename);
			$filename = str_replace("<", '-', $filename);
			$filename = str_replace(">", '-', $filename);
			$filename = str_replace("|", '-', $filename);
			$filename = str_replace("\\", -'', $filename);
			$filename = str_replace("~", '-', $filename);
			$filename = str_replace("[", '-', $filename);
			$filename = str_replace("]", '-', $filename);
			$filename = str_replace("(", '-', $filename);
			$filename = str_replace(")", '-', $filename);
			$filename = str_replace("^", '-', $filename);
			$filename = str_replace("!", '-', $filename);
			$filename = str_replace("{", '-', $filename);
			$filename = str_replace("}", '-', $filename);
			$filename = str_replace("'", '-', $filename);
			
			$filename = str_replace('#', '-hash-', $filename);
			$filename = str_replace('%', '-pct-', $filename);
			$filename = str_replace('&', '-and-', $filename);
			$filename = str_replace('@', '-at-', $filename);
			$filename = str_replace("=", '-eq-', $filename);
			
			$filename = str_replace("  ", ' ', $filename);
			$filename = str_replace("  ", ' ', $filename);
			$filename = str_replace("  ", ' ', $filename);
			
			$filename = str_replace("--", '-', $filename);
			$filename = str_replace("--", '-', $filename);
			$filename = str_replace("--", '-', $filename);
			
			mysqli_query($conn, "
				update t_queue
				set
					filename = '" . mysqli_real_escape_string($conn, $filename) . "',
					status = 'F'
				where
					id_queue = " . $queue['id_queue'] . "
				");
			
		}
		
		
	}
	
	
	$id_grab = $settings->val('custom_downloads_id_grab',0);
	
	mysqli_query($conn, "
		
		insert into t_grab_file (id_grab, full_url, full_path, type) 
		select 
			" . $id_grab . ", 
			q.url,  
			concat(q.directory, q.filename),
			case when q.status = 'V' then 'youtube-dl' else '' end
		from t_queue q
			left join t_grab_file gf on gf.full_url = q.url and gf.id_grab = " . $id_grab . "
		where  
			q.status in ('V', 'D')
			and q.directory <> ''
			and q.filename <> ''
			and gf.id_grab_file is null 
		
		");
		
	mysqli_query($conn, "
		update t_queue q
			join t_grab_file gf on gf.full_url = q.url and gf.id_grab = " . $id_grab . "
		set	
			q.status = 'A'
		where
			q.status in ('V', 'D')
			and q.directory <> ''
			and q.filename <> ''
			
		");
	

	$task->setIsRunning(false);
	
}

?>