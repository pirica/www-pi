<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

require dirname(__FILE__).'/../_core/components/phpQuery/phpQuery.php';

include 'connections.php';
include "class.myrssparser.php"; 
include "class.myatomparser.php"; 

$date_start = time();


// clean up entries for inactive feeds
mysqli_query($conn, "
	
	update t_feed_entry
	set
		active = 0,
		date_deleted = now()
	where
		id_feed in (select id_feed from t_feed where active = 0)
		and active = 1
		
	");

	
// clean up entries older than a week
mysqli_query($conn, "
	
	delete from t_feed_entry
	where
		active = 0
		and date_deleted < now() - interval 1 week
		
	");
	

$qry_feeds = mysqli_query($conn, "
	
	select
		f.id_feed,
		f.url,
		f.title,
		f.description,
		f.parser,
		ifnull(f.parse_max_items,-1) as parse_max_items,
		f.feed_items,
		count(fe.id_feed_entry) as entries
	from t_feed f
	left join t_feed_entry fe on fe.id_feed = f.id_feed and fe.is_read = 1 and fe.active = 1
	where
		f.active = 1
		and ifnull(f.date_last_checked, '1970-01-01') <= now() - interval (ifnull(f.refresh, " . $settings->val('default_interval_check_feeds_minutes', 60) . ")) minute
		and hour(now()) between " . $settings->val('check_feeds_from_hour', 6) . " and " . $settings->val('check_feeds_to_hour', 23) . "
	group by
		f.id_feed,
		f.url,
		f.title,
		f.description
		
	");

while ($feeds = mysqli_fetch_array($qry_feeds)) 
{
	
	echo 'feed ' . $feeds['title'] . ' (ID:' .  $feeds['id_feed'] . ")\n";
	echo ' -> started on ' . date('Y-m-d H:i:s', time()) . "\n";
	
	$entries_to_keep = 100;
	if($feeds['feed_items'] != '' && $feeds['feed_items'] > 0)
	{
		$entries_to_keep = $feeds['feed_items'] * 2;
	}
	
	if($feeds['entries'] > $entries_to_keep){
		
		// delete read entries until 100 remain
		$qry_feed_entries_del = mysqli_query($conn, "
			
			select
				fe.id_feed_entry
				
			from t_feed_entry fe
			where
				fe.id_feed = " . $feeds['id_feed'] . "
				and fe.active = 1
				and fe.is_read = 1
			order by
				fe.id_feed_entry
				
			");
		
		$remaining_entries = $feeds['entries'];
		
		while (($feedentrydel = mysqli_fetch_array($qry_feed_entries_del)) && $remaining_entries > 100)
		{
			mysqli_query($conn, "
				
				update t_feed_entry
				set
					active = 0,
					date_deleted = now()
				where
					id_feed_entry = " . $feedentrydel['id_feed_entry'] . "
					
				");
			
			$remaining_entries--;
		}
		
		echo ' -> ' . ($feeds['entries'] - $remaining_entries) . " read entries deleted\n";
		
	}
	
	// get current entries
	$qry_feed_entries = mysqli_query($conn, "
		
		select
			fe.id_feed_entry,
			fe.title,
			fe.link,
			fe.description,
			fe.pubdate
			
		from t_feed_entry fe
		where
			fe.id_feed = " . $feeds['id_feed'] . "
			and fe.active = 1
			
		");
		
	$err = 0;
	
	// set "start"
	mysqli_query($conn, "update t_feed set date_start = now() where id_feed = " . $feeds['id_feed'] . "");
	
	
	try
	{
		if($feeds['parser'] != ''){
			
		}
		else if(strpos($feeds['url'], 'feed.atom') !== false){
			$rss_parser = new myAtomParser($feeds['url']);
			$rss = $rss_parser->getRawOutput();
		}
		else {
			$rss_parser = new myRSSParser($feeds['url']);
			$rss = $rss_parser->getRawOutput();
		}
	}
	catch(Exception $e)
	{
		$err = 1;
		echo ' -> cannot parse feed, error:  ' . $e->getMessage() . "\n\n";
	}
	
	// feed in error...
	if($err == 1)
	{
		
	}
	// no errors, continue
	else
	{
		$items = 0;
		$items_ins = 0;
		
		if($feeds['parser'] != ''){
			$items++;
			
			$title = '';
			$link = '';
			$description = '';
			$pubdate = time();
			
			include ('parsers/' . $feeds['parser']);
			
			$found = 0;
			// loop over feed items
			while (($feedentry = mysqli_fetch_array($qry_feed_entries)) && $found == 0) 
			{
				if(($link != '' && $feedentry['link'] == $link) || ($title != '' && $feedentry['title'] == $title))
				{
					$found = 1;
				}
			}
			
			// if already inserted, ignore
			if($found == 1 || ($description == '' && $title == ''))
			{
				
			}
			// else, new item: insert
			else 
			{
				if($pubdate == '')
				{
					$pubdate = 'NULL';
				}
				else
				{
					$pubdate = "'".date('Y-m-d H:i:s', strtotime($pubdate))."'";
				}
				
				//$description = str_replace('"', '\"', $description);
				$description = str_replace("'", "\'", $description);
				
				// if 'slashdot':
				/*if('' . $feeds['id_feed'] == '1')
				{
					// remove html from entry
					$description = explode('<p>', $description, 1)[0];
				}*/
				
				mysqli_query($conn, "
					insert into t_feed_entry 
					(
						id_feed,
						title,
						link,
						description,
						pubdate
					)
					values
					(
						" . $feeds['id_feed'] . ",
						'" . mysqli_real_escape_string($conn, $title) . "',
						'" . mysqli_real_escape_string($conn, $link) . "',
						'" . $description . "',
						" . $pubdate . "
					)
					
					");
				
				$items_ins++;
			}
			
			if(mysqli_num_rows($qry_feed_entries) >= 1){
				mysqli_data_seek($qry_feed_entries, 0);
			}
			
		}
		else {
			$start_tag = key($rss);
			
			// loop over rss items
			if($start_tag == 'RSS')
			{
				foreach($rss['RSS']["CHANNEL"] as $channel)
				{
					foreach($channel['ITEM'] as $item)
					{
						$items++;
						
						if($feeds['parse_max_items'] > 0 && $items >= $feeds['parse_max_items'])
						{
							break;
						}
						
						$title = '';
						$link = '';
						$description = '';
						$pubdate = '';
						if(isset($item['TITLE']))	$title = $item['TITLE'];
						if(isset($item['LINK']))	$link = $item['LINK'];
						if(isset($item['DESCRIPTION']))	$description = $item['DESCRIPTION'];
						if(isset($item['PUBDATE']))	$pubdate = $item['PUBDATE'];
						else if(isset($item['DATE']))	$pubdate = $item['DATE'];
						
						$found = 0;
						// loop over feed items
						while (($feedentry = mysqli_fetch_array($qry_feed_entries)) && $found == 0) 
						{
							if(($link != '' && $feedentry['link'] == $link) || ($title != '' && $feedentry['title'] == $title))
							{
								$found = 1;
							}
						}
						
						// if already inserted, ignore
						if($found == 1)
						{
							
						}
						// else, new item: insert
						else 
						{
							if($pubdate == '')
							{
								$pubdate = 'NULL';
							}
							else
							{
								$pubdate = "'".date('Y-m-d H:i:s', strtotime($pubdate))."'";
							}
							
							//$description = str_replace('"', '\"', $description);
							$description = str_replace("'", "\'", $description);
							
							// if 'slashdot':
							/*if('' . $feeds['id_feed'] == '1')
							{
								// remove html from entry
								$description = explode('<p>', $description, 1)[0];
							}*/
							
							
							mysqli_query($conn, "
								insert into t_feed_entry 
								(
									id_feed,
									title,
									link,
									description,
									pubdate
								)
								values
								(
									" . $feeds['id_feed'] . ",
									'" . mysqli_real_escape_string($conn, $title) . "',
									'" . mysqli_real_escape_string($conn, $link) . "',
									'" . $description . "',
									" . $pubdate . "
								)
								
								");
							$items_ins++;
							
						}
						
						if(mysqli_num_rows($qry_feed_entries) >= 1){
							mysqli_data_seek($qry_feed_entries, 0);
						}
					}
				}
			}
			// loop over rss items
			else if($start_tag == 'RDF:RDF')
			{
				//foreach($rss['RDF:RDF']["CHANNEL"] as $channel)
				{
					foreach($rss[$start_tag]['ITEM'] as $item)
					{
						$items++;
						
						if($feeds['parse_max_items'] > 0 && $items >= $feeds['parse_max_items'])
						{
							break;
						}
						
						$title = '';
						$link = '';
						$description = '';
						$pubdate = '';
						if(isset($item['TITLE']))	$title = $item['TITLE'];
						if(isset($item['LINK']))	$link = $item['LINK'];
						if(isset($item['DESCRIPTION']))	$description = $item['DESCRIPTION'];
						if(isset($item['PUBDATE']))	$pubdate = $item['PUBDATE'];
						else if(isset($item['DATE']))	$pubdate = $item['DATE'];
						
						$found = 0;
						// loop over feed items
						while (($feedentry = mysqli_fetch_array($qry_feed_entries)) && $found == 0) 
						{
							if(($link != '' && $feedentry['link'] == $link) || ($title != '' && $feedentry['title'] == $title)){
								$found = 1;
							}
						}
						
						// if already inserted, ignore
						if($found == 1)
						{
						
						}
						// else, new item: insert
						else 
						{
							if($pubdate == '')
							{
								$pubdate = 'NULL';
							}
							else
							{
								$pubdate = "'".date('Y-m-d H:i:s', strtotime($pubdate))."'";
							}
							
							//$description = str_replace('"', '\"', $description);
							$description = str_replace("'", "\'", $description);
							
							// if 'slashdot':
							/*if('' . $feeds['id_feed'] == '1')
							{
								// remove html from entry
								$description = explode('<p>', $description, 1)[0];
							}*/
							
							mysqli_query($conn, "
								insert into t_feed_entry 
								(
									id_feed,
									title,
									link,
									description,
									pubdate
								)
								values
								(
									" . $feeds['id_feed'] . ",
									'" . mysqli_real_escape_string($conn, $title) . "',
									'" . mysqli_real_escape_string($conn, $link) . "',
									'" . $description . "',
									" . $pubdate . "
								)
								
								");
							$items_ins++;
							
						}
						
						if(mysqli_num_rows($qry_feed_entries) >= 1){
							mysqli_data_seek($qry_feed_entries, 0);
						}
					}
				}
			}
			// loop over atom items
			else if($start_tag == 'FEED')
			{
				//foreach($rss['RDF:RDF']["CHANNEL"] as $channel)
				{
					foreach($rss[$start_tag]['ENTRY'] as $item)
					{
						$items++;
						
						if($feeds['parse_max_items'] > 0 && $items >= $feeds['parse_max_items'])
						{
							break;
						}
						
						$title = '';
						$link = '';
						$description = '';
						$pubdate = '';
						if(isset($item['TITLE']))	$title = $item['TITLE'];
						if(isset($item['LINK']))	$link = $item['LINK'];
						if(isset($item['CONTENT']))	$description = $item['CONTENT'];
						if(isset($item['UPDATED']))	$pubdate = $item['UPDATED'];
						else if(isset($item['PUBLISHED']))	$pubdate = $item['PUBLISHED'];
						
						$found = 0;
						// loop over feed items
						while (($feedentry = mysqli_fetch_array($qry_feed_entries)) && $found == 0) 
						{
							if(($link != '' && $feedentry['link'] == $link) || ($title != '' && $feedentry['title'] == $title)){
								$found = 1;
							}
						}
						
						// if already inserted, ignore
						if($found == 1)
						{
						
						}
						// else, new item: insert
						else 
						{
							if($pubdate == '')
							{
								$pubdate = 'NULL';
							}
							else
							{
								$pubdate = "'".date('Y-m-d H:i:s', strtotime($pubdate))."'";
							}
							
							//$description = str_replace('"', '\"', $description);
							$description = str_replace("'", "\'", $description);
							
							// if 'slashdot':
							/*if('' . $feeds['id_feed'] == '1')
							{
								// remove html from entry
								$description = explode('<p>', $description, 1)[0];
							}*/
							
							if($link != '' || $title != ''){
								
								mysqli_query($conn, "
									insert into t_feed_entry 
									(
										id_feed,
										title,
										link,
										description,
										pubdate
									)
									values
									(
										" . $feeds['id_feed'] . ",
										'" . mysqli_real_escape_string($conn, $title) . "',
										'" . mysqli_real_escape_string($conn, $link) . "',
										'" . $description . "',
										" . $pubdate . "
									)
									
									");
								
								$items_ins++;
								
							}
						}
						
						if(mysqli_num_rows($qry_feed_entries) >= 1){
							mysqli_data_seek($qry_feed_entries, 0);
						}
					}
				}
			}
		}
		
		// set "last checked"
		mysqli_query($conn, "update t_feed set date_last_checked = '" . mysqli_real_escape_string($conn, date('Y-m-d H:i:s', $date_start)) . "', date_end = now(), feed_items = " . $items . " where id_feed = " . $feeds['id_feed'] . "");
		
		
		//echo ' -> ' . $items . " items in feed\n";
		if($items_ins > 0){
			echo ' -> ' . $items_ins . " items inserted\n";
		}
		//echo ' -> completed on ' . date('Y-m-d H:i:s', time()) . "\n";
		echo "\n";
		
	}
	
	flush();
	
}

?>