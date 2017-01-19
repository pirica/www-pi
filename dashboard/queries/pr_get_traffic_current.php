<?php

$qry_timings_max = mysqli_query($conn, "
	select
		max(DATE_FORMAT(r.date, '%Y%m%d%H%i')) as latest,
		max(DATE_FORMAT(r.date, '%H:%i')) as latest_formatted,
	from reistijden r
	");
$timings_max = mysqli_fetch_array($qry_timings_max);

$qry_timings = mysqli_query($conn, "
	
	select
		k.id as knooppuntId,
		k.name,
		k.traject,
		r.date,
		r.value
		
	from knooppunten k
		left join reistijden r on r.knooppuntId = k.id
			and DATE_FORMAT(r.date, '%Y%m%d%H%i') = '" . $timings_max['latest'] . "'
	
	order by
		r.date desc,
		k.sort_order asc
		
	");

$timings = array();

while($a_timings = mysqli_fetch_array($qry_timings))
{
	$timings[$a_timings['knooppuntId']] = $a_timings['value'];
}

?>