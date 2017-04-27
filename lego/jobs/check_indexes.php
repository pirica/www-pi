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
	
	
	
	$qry = mysqli_query($conn, "
		select
			year,
			page
		from indexByYear
		where
			indexed = 0
		#limit 1, 1
		");

	while ($pages = mysqli_fetch_array($qry)) {
		
		$str = file_get_contents('http://lego.brickinstructions.com/search/year/' . $pages['year'] . ($pages['page'] == 1 ? '' : '/' . $pages['page']));

		$doc = phpQuery::newDocumentHTML($str);

		$sets = $doc->find('.setBox a');
		foreach ($sets as $set)
		{
			if(stripos(pq($set)->html(), '<img') === false)
			{
				$linkparts = explode('/', pq($set)->attr('href'));
				$setnr = '';
				$setcode = '';
				if(count($linkparts) > 3)
				{
					$setnr = $linkparts[count($linkparts) - 2];
					$setcode = $linkparts[count($linkparts) - 1];
					mysqli_query($conn, "
						insert into indexByPage
						(
							`year`,
							`set`,
							code,
							name
						)
						select
							`year`,
							`set`,
							code,
							name
						from (select 
							'" . mysqli_real_escape_string($conn, $pages['year']) . "' as `year`,
							'" . mysqli_real_escape_string($conn, $setnr) . "' as `set`,
							'" . mysqli_real_escape_string($conn, $setcode) . "' as code,
							'" . mysqli_real_escape_string($conn, pq($set)->html()) . "' as name
						) tmp
						where not exists(
							select * from indexByPage
							where `set` = '" . mysqli_real_escape_string($conn, $setnr) . "'
						)
					");
				}
			}
		}
		
		mysqli_query($conn, "
			update indexByYear 
			set indexed = 1 
			where year = '" . mysqli_real_escape_string($conn, $pages['year']) . "' 
			and page = '" . mysqli_real_escape_string($conn, $pages['page']) . "' 
			
			");
	}
	
	
	$qry = mysqli_query($conn, "
		select
			`year`,
			`set`,
			code,
			name
		from indexByPage
		where
			indexed = 0
		#limit 1, 1
		");

	while ($pages = mysqli_fetch_array($qry)) {
		
		$str = file_get_contents('http://lego.brickinstructions.com/en/lego_instructions/set/' . $pages['set'] . '/' . $pages['code']);

		$doc = phpQuery::newDocumentHTML($str);

		$images = $doc->find('#instructionContainer a');
		foreach ($images as $image)
		{
			mysqli_query($conn, "
				insert into indexPage
				(
					`set`,
					image_url
				)
				select
					`set`,
					image_url
				from (select 
					'" . mysqli_real_escape_string($conn, $pages['set']) . "' as `set`,
					'" . mysqli_real_escape_string($conn, pq($image)->attr('href')) . "' as image_url
				) tmp
				where not exists(
					select * from indexPage
					where `set` = '" . mysqli_real_escape_string($conn, $pages['set']) . "'
						and image_url = '" . mysqli_real_escape_string($conn, pq($image)->attr('href')) . "'
				)
			");
			
		}
		
		mysqli_query($conn, "
			update indexByPage 
			set indexed = 1 
			where `set` = '" . mysqli_real_escape_string($conn, $pages['set']) . "' 
			
			");
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