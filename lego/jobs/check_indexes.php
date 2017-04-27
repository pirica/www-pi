<?php
set_time_limit(3600);
require dirname(__FILE__).'/../connections.php';
require dirname(__FILE__).'/../functions.php';
require dirname(__FILE__).'/../../_core/appinit.php';

require dirname(__FILE__).'/../../_core/components/phpQuery/phpQuery.php';


if(!$task->getIsRunning())
{
	$task->setIsRunning(true);
	
	if($settings->val('reindex_all', 0) == 1)
	{
		
		$str = file_get_contents('http://lego.brickinstructions.com/en/showallyears');

		$doc = phpQuery::newDocumentHTML($str);

		$links = $doc->find('.yearTable a');
		foreach ($links as $link)
		{
			mysqli_query($conn, "
				insert into indexYear
				(
					year
				)
				select
					year
				from (select '" . mysqli_real_escape_string($conn, pq($link)->html()) . "' as year) tmp
				where not exists(
					select * from indexYear where year = '" . mysqli_real_escape_string($conn, pq($link)->html()) . "'
				)
			");
			
			mysqli_query($conn, "
				insert into indexByYear
				(
					year,
					page
				)
				select
					year,
					page
				from (select 
					'" . mysqli_real_escape_string($conn, pq($link)->html()) . "' as year,
					1 as page
				) tmp
				where not exists(
					select * from indexByYear
					where year = '" . mysqli_real_escape_string($conn, pq($link)->html()) . "'
						and page = 1
				)
			");
		}
		
		mysqli_query($conn, "update indexYear set indexed = 0");
		mysqli_query($conn, "update indexByYear set indexed = 0");
		mysqli_query($conn, "update indexByPage set indexed = 0");
	}
	
	/*
	->attr('alt');
	$description = $doc->find('.kadercomic')->html();
	$description = str_replace('src="', 'src="http://www.niconarsinferno.be/', $description);
*/
	
	
	$qry = mysqli_query($conn, "
		select
			year
		from indexYear
		where
			indexed = 0
		#limit 1, 1
		");

	while ($years = mysqli_fetch_array($qry)) {
		
		$str = file_get_contents('http://lego.brickinstructions.com/search/year/' . $years['year']);

		$doc = phpQuery::newDocumentHTML($str);

		$links = $doc->find('.paginateGroup a');
		foreach ($links as $link)
		{
			mysqli_query($conn, "
				insert into indexByYear
				(
					year,
					page
				)
				select
					year,
					page
				from (select 
					'" . mysqli_real_escape_string($conn, $years['year']) . "' as year,
					'" . mysqli_real_escape_string($conn, pq($link)->html()) . "' as page
				) tmp
				where not exists(
					select * from indexByYear
					where year = '" . mysqli_real_escape_string($conn, $years['year']) . "'
						and page = '" . mysqli_real_escape_string($conn, pq($link)->html()) . "'
				)
			");
		}
		
		mysqli_query($conn, "update indexYear set indexed = 1 where year = '" . mysqli_real_escape_string($conn, $years['year']) . "' ");
	}
	
	/*
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
	
	*/
	

	$task->setIsRunning(false);
	
}

?>