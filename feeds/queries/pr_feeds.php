<?php
	
$qry_feeds = mysql_query("
	
	select
		f.id_feed,
		f.title,
		f.url,
		f.refresh,
		f.date_last_checked,
		
		f.open_url,
		f.desktop_only,
		
		sum(case when fe.id_feed_entry is not null and ifnull(fe.is_read,0) = 0 then 1 else 0 end) as entries,
		max(ifnull(case when fe.pubdate = '0000-00-00 00:00:00' then null else fe.pubdate end, fe.date_inserted)) as last_entry_date,
		
		f.date_start,
		f.date_end,
		
		case 
			when f.date_start is null then 0
			when f.date_start is not null and f.date_end is null then 1
			when f.date_start > f.date_end then 1
			else 0
		end as check
	
	from t_feed f
		left join t_feed_entry fe on fe.id_feed = f.id_feed
			and fe.active = 1
			#and ifnull(fe.is_read,0) = 0
	where
		f.id_user = " . $_SESSION['user_id'] . "
		and f.active = 1
		#and ifnull(f.desktop_only, 0) <= " . $desktop . "
	
	group by
		f.id_feed,
		f.title,
		f.open_url,
		f.desktop_only
		
	order by
		f.title asc
		
	", $conn) or die(mysql_error());
	
?>