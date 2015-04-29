<?php

include 'connection.php';

/*
pseudo:
	- loop over t_log_track's, where id_track is empty, ordered by 'time'
	- check if any t_track record exists where 'time' between 'date_start' and 'date_end'  + offset (5 minutes?)
		- if yes:
			- enter id_track in t_log_track
			- update t_track 'end' columns
		- if not:
			- insert new t_track (both 'start' and 'end'columns equal the same value)

*/

function getaddress($lat,$lng)
{
	$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
	$json = @file_get_contents($url);
	$data = json_decode($json);
	$status = $data->status;
	
	if($status == "OK")
		return $data->results[0]->formatted_address;
	else
		return false;
}


$qry_log = mysql_query("
	select
		*
		, time - interval 30 minute as date_start
		, time + interval 30 minute as date_end
	from t_log_track 
	where id_track is null 
	order by time asc
	");

while ($log = mysql_fetch_array($qry_log))
{
	$qry_track = mysql_query("
		select * from t_track 
		where 
			active = 1
			and '" . $log['time'] . "' between (date_start - interval 30 minute) and (date_end + interval 30 minute)
		");
	
	$id_track = -1;
	
	if(mysql_num_rows($qry_track) == 0)
	{
		mysql_query("
			insert into t_track
			(
				date_start,
				date_end,
				start_lat,
				start_lon,
				end_lat,
				end_lon
			)
			values
			(
				'" . $log['time'] . "',
				'" . $log['time'] . "',
				" . $log['lat'] . ",
				" . $log['lon'] . ",
				" . $log['lat'] . ",
				" . $log['lon'] . "
			)
			");
			
		$id_track = mysql_insert_id();
		
	}
	else
	{
		$id_track = mysql_fetch_array($qry_track)['id_track'];
		
		mysql_query("
			update t_track
			set
				date_end = '" . $log['time'] . "',
				end_lat = " . $log['lat'] . ",
				end_lon = " . $log['lon'] . "
			
			where
				id_track = " . $id_track . "
			");
	}
	
	mysql_query("
		update t_log_track
		set
			id_track = " . $id_track . "
		where
			id_log_track = " . $log['id_log_track'] . "
		");
}




$qry_track = mysql_query("
	select * from t_track 
	where 
		active = 1
		and start_description is null
	");

while ($track = mysql_fetch_array($qry_track))
{
	
	$start_description = getaddress($track['start_lat'], $track['start_lon']);
	
	if($start_description)
	{
		mysql_query("
			update t_track
			set
				start_description = '" . $start_description . "'
			where
				id_track = " . $track['id_track'] . "
			");
	}
	
}


$qry_track = mysql_query("
	select * from t_track 
	where 
		active = 1
		and end_description is null
	");

while ($track = mysql_fetch_array($qry_track))
{
	
	$end_description = getaddress($track['end_lat'], $track['end_lon']);
	
	if($end_description)
	{
		mysql_query("
			update t_track
			set
				end_description = '" . $end_description . "'
			where
				id_track = " . $track['id_track'] . "
			");
	}
}

?>