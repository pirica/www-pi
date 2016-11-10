<?php 
include "class.myrssparser.php"; 

// where is the feed located?
//$url = "http://tweakblogs.net/feed/";
//$url = "http://feeds.feedburner.com/oatmealfeed";
$url = "http://what-if.xkcd.com/feed.atom";

// create object to hold data and display output
$rss_parser = new myRSSParser($url);

$rss = $rss_parser->getRawOutput();
//print_r($rss);
/*
// returns string containing HTML
$outputhtml = $rss_parser->getOutput();
echo $outputhtml;

// returns raw array data
$outputraw = $rss_parser->getRawOutput();
echo print_r($outputraw);


// returns item array output
$outputitems = $rss_parser->getOutputItems();
echo print_r($outputitems);
*/

foreach($rss['FEED']['ENTRY'] as $item)
				{
					
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
					/*while (($feedentry = mysqli_fetch_array($qry_feed_entries)) && $found == 0) 
					{
                        if(($link != '' && $feedentry['link'] == $link) || ($title != '' && $feedentry['title'] == $title)){
							$found = 1;
						}
					}*/
					
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
						
						echo 'title=' . $title . '<br>';
						echo 'link=' . $link . '<br>';
						echo 'description=' . $description . '<br>';
						echo 'pubdate=' . $pubdate . '<br>';
					}
					
				}
?>