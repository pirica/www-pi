<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

include 'connections.php';
include "class.myrssparser.php"; 
include "class.myatomparser.php"; 


$qry_feeds = mysql_query("
	
	select
		f.id_feed,
		f.url,
		f.title,
		f.description,
		count(fe.id_feed_entry) as entries
	from t_feed f
	left join t_feed_entry fe on fe.id_feed = f.id_feed and fe.is_read = 1 and fe.active = 1
	where
		f.active = 1
		and ifnull(f.date_last_checked, '1970-01-01') < now() - interval (ifnull(f.refresh, " . $settings->val('default_interval_check_feeds_minutes', 60) . ")) minute
		and hour(now()) between " . $settings->val('check_feeds_from_hour', 6) . " and " . $settings->val('check_feeds_to_hour', 23) . "
	group by
		f.id_feed,
		f.url,
		f.title,
		f.description
		
	", $conn);

while ($feeds = mysql_fetch_array($qry_feeds)) 
{
	
	echo 'feed ' . $feeds['title'] . ' (ID:' .  $feeds['id_feed'] . ")\n";
	echo ' -> started on ' . date('Y-m-d H:i:s', time()) . "\n";
	
	if($feeds['entries'] > 100){
		
		// delete read entries until 100 remain
		$qry_feed_entries_del = mysql_query("
			
			select
				fe.id_feed_entry
				
			from t_feed_entry fe
			where
				fe.id_feed = " . $feeds['id_feed'] . "
				and fe.active = 1
				and fe.is_read = 1
			order by
				fe.id_feed_entry
				
			", $conn);
		
		$remaining_entries = $feeds['entries'];
		
		while (($feedentrydel = mysql_fetch_array($qry_feed_entries_del)) && $remaining_entries > 100)
		{
			mysql_query("
				
				update t_feed_entry
				set
					active = 0,
					date_deleted = now()
				where
					id_feed_entry = " . $feedentrydel['id_feed_entry'] . "
					
				", $conn);
			
			$remaining_entries--;
		}
		
		echo ' -> ' . ($feeds['entries'] - $remaining_entries) . " read entries deleted\n";
		
	}
	
	// clean up entries older than a month
	mysql_query("
		
		delete from t_feed_entry
		where
			active = 0,
			and date_deleted < now() - interval 1 month
			
		", $conn);
	
	// get current entries
	$qry_feed_entries = mysql_query("
		
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
			
		", $conn);
		
	$err = 0;
	
	try
	{
		if(strpos($feeds['url'], 'feed.atom') !== false){
			$rss_parser = new myAtomParser($feeds['url']);
		}
		else {
			$rss_parser = new myRSSParser($feeds['url']);
		}
		$rss = $rss_parser->getRawOutput();
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
		
		$start_tag = key($rss);
		
		// loop over rss items
		if($start_tag == 'RSS')
		{
			foreach($rss['RSS']["CHANNEL"] as $channel)
			{
				foreach($channel['ITEM'] as $item)
				{
					$items++;
					
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
					while (($feedentry = mysql_fetch_array($qry_feed_entries)) && $found == 0) 
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
						
						mysql_query("
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
								'" . mysql_real_escape_string($title) . "',
								'" . mysql_real_escape_string($link) . "',
								'" . $description . "',
								" . $pubdate . "
							)
							
							", $conn);
						
						$items_ins++;
					}
					
					if(mysql_num_rows($qry_feed_entries) >= 1){
						mysql_data_seek($qry_feed_entries, 0);
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
					while (($feedentry = mysql_fetch_array($qry_feed_entries)) && $found == 0) 
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
						
						mysql_query("
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
								'" . mysql_real_escape_string($title) . "',
								'" . mysql_real_escape_string($link) . "',
								'" . $description . "',
								" . $pubdate . "
							)
							
							", $conn);
						
						$items_ins++;
					}
					
					if(mysql_num_rows($qry_feed_entries) >= 1){
						mysql_data_seek($qry_feed_entries, 0);
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
					while (($feedentry = mysql_fetch_array($qry_feed_entries)) && $found == 0) 
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
							mysql_query("
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
									'" . mysql_real_escape_string($title) . "',
									'" . mysql_real_escape_string($link) . "',
									'" . $description . "',
									" . $pubdate . "
								)
								
								", $conn);
							
							$items_ins++;
						}
					}
					
					if(mysql_num_rows($qry_feed_entries) >= 1){
						mysql_data_seek($qry_feed_entries, 0);
					}
				}
			}
		}
		
		// set "last checked"
		mysql_query("update t_feed set date_last_checked = now() where id_feed = " . $feeds['id_feed'] . "", $conn);
		
		
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