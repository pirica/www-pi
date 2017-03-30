<?php
set_time_limit(3600);
include '../connections.php';
include '../functions.php';
require dirname(__FILE__).'/../../_core/appinit.php';


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
					status = 'F'
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

	$task->setIsRunning(false);
	
}

?>