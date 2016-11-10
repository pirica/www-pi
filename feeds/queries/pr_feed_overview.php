<?php
	
$qry_feed_overview = mysqli_query($conn, "
	
	select
		f.id_feed,
		f.title,
		f.open_url,
		
		count(id_feed_entry) as entries,
		max(pubdate) as last_entry_date
	
	from t_feed f
		join t_feed_entry fe on fe.id_feed = f.id_feed
			and fe.active = 1
			and ifnull(fe.is_read,0) = 0
	where
		f.id_user = " . $_SESSION['user_id'] . "
		and f.active = 1
		and ifnull(f.desktop_only, 0) <= " . $desktop . "
	
	group by
		f.id_feed,
		f.title,
		f.open_url
		
	order by
		f.title asc
		
	");
	
?>