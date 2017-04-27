<?php
set_time_limit(3600);
require dirname(__FILE__).'/../connections.php';
require dirname(__FILE__).'/../functions.php';
require dirname(__FILE__).'/../../_core/appinit.php';

require dirname(__FILE__).'/../../_core/components/phpQuery/phpQuery.php';


if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	
	$str = file_get_contents('http://lego.brickinstructions.com/en/showallyears');

	$doc = phpQuery::newDocumentHTML($str);

	$links = $doc->find('.yearTable a');
	foreach ($links as $link)
	{
		mysqli_query($conn, "
			replace into indexYear
			(
				year
			)
			values
			(
				'" . mysqli_real_escape_string($conn, pq($link)->html()) . "'
			)
		");
	}
	
	/*
	->attr('alt');
	$description = $doc->find('.kadercomic')->html();
	$description = str_replace('src="', 'src="http://www.niconarsinferno.be/', $description);
*/
	
	
	/*
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
		
		$ytdl = shell_exec('/usr/bin/youtube-dl --get-filename -o "%(title)s.%(ext)s" ' . $queue['url']);
		$filename = '';
		
		// youtube-dl found a file, update
		if(isset($ytdl) && $ytdl != ''){
			$type = 'youtube-dl';
			$filename = $ytdl;
			
			$filename = str_replace("\r", ' ', str_replace("\n", ' ', str_replace("\t", ' ', $filename)));
			
			mysqli_query($conn, "
				update t_queue
				set
					filename = trim('" . mysqli_real_escape_string($conn, $filename) . "'),
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
		
		insert into subsonic.playlistEntriesToAdd (playlistId, songFilename) 
		select 
			q.playlistId,  
			q.filename
		from t_queue q
			left join subsonic.playlistEntriesToAdd pea on pea.playlistId = q.playlistId and pea.songFilename = q.filename
		where  
			q.status in ('V', 'D')
			and q.directory <> ''
			and q.filename <> ''
			and ifnull(q.playlistId,0) > 0
			and pea.id is null 
		
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
	*/
	

	$task->setIsRunning(false);
	
}

?>