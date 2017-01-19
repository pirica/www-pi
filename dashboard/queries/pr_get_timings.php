<?php

$naar_werk = date("H:i", time()) <= '12:00' ? 1 : 0;

$timings_start = date("YmdHi", strtotime('-5 minute', time()));
$timings_end = date("YmdHi", time());

$qry_timings = mysqli_query($conn, "
	
	select
		k.id as knooppuntId,
		k.name,
		k.traject,
		r.date,
		r.value
		
	from knooppunten k
		left join reistijden r on r.knooppuntId = k.id
			and DATE_FORMAT(r.date, '%Y%m%d%H%i') > '" . $timings_start . "'
			and DATE_FORMAT(r.date, '%Y%m%d%H%i') <= '" . $timings_end . "'
		
	where
		k.naar = " . $naar_werk . "
		
	order by
		r.date desc,
		k.sort_order asc
		
	");

?>