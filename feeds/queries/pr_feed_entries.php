<?php
	
$qry_feed_entries = mysql_query("
	
	select
		f.id_feed,
		f.title,
		
		f.open_url,
		
		fe.id_feed_entry,
		fe.title as entry_title,
		fe.link as entry_link,
		case 
			when f.id_feed = 1
			then SUBSTRING_INDEX(fe.description, '<p>', 1)
			else fe.description
		end as entry_description,
		fe.pubdate
	
	from t_feed f
		join t_feed_entry fe on fe.id_feed = f.id_feed
			and fe.active = 1
			and ifnull(fe.is_read,0) = 0
	where
		f.id_feed = " . $id_feed . "
	
	order by
		fe.pubdate asc,
		fe.id_feed_entry asc
		
	", $conn) or die(mysql_error());
	
?>