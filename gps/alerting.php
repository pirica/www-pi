<?php

include 'connection.php';
require dirname(__FILE__).'/../messages/functions.php';

/*
$qry_log = mysql_query("
	select
		lt.lat,
		lt.lon,
		
		(p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) as plac_lat,
		(p.lon_right - ((p.lon_right - p.lon_left) / 2)) as place_lon,
		
		lt.accuracy,
		lt.heading,
		u.id_user,
		p.id_place,
		
		(6371 * acos(cos(radians(lt.lat)) * cos(radians( (p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) )) * cos(radians( (p.lon_right - ((p.lon_right - p.lon_left) / 2)) ) - radians(lt.lon)) + sin(radians(lt.lat)) * sin(radians( (p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) )))) as distance_kms,
		(3959 * acos(cos(radians(lt.lat)) * cos(radians( (p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) )) * cos(radians( (p.lon_right - ((p.lon_right - p.lon_left) / 2)) ) - radians(lt.lon)) + sin(radians(lt.lat)) * sin(radians( (p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) )))) as distance_miles
		
	from gps.t_log_track lt
	left join users.t_user u on u.username = lt.username
	left join gps.t_place p on p.active = 1
		#and (6371 * acos(cos(radians(lt.lat)) * cos(radians( (p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) )) * cos(radians( (p.lon_right - ((p.lon_right - p.lon_left) / 2)) ) - radians(lt.lon)) + sin(radians(lt.lat)) * sin(radians( (p.lat_top - ((p.lat_top - p.lat_bottom) / 2)) )))) 
		
	where
		lt.time > now() - interval 10 minute
		and lt.accuracy >= 100
		
	order by lt.time desc
	
	limit 1, 1
	
	", $mysql_conn) or die(mysql_error($mysql_conn));
*/

$qry_log = mysql_query("
	select
		lt.lat,
        lt.lon,
        lt.accuracy,
        lt.heading,
		u.id_user,
		p.id_place
	from gps.t_log_track lt
	left join users.t_user u on u.username = lt.username
	left join gps.t_place p on p.active = 1
		and lt.lat between p.lat_bottom and p.lat_top
		and lt.lon between p.lon_left and p.lon_right
		
	where
		lt.time > now() - interval 10 minute
		and lt.accuracy < 100
		
	order by lt.time desc
	
	limit 1, 1
	
	", $mysql_conn) or die(mysql_error($mysql_conn));
	
	
while ($log = mysql_fetch_array($qry_log))
{

	mysql_query("update gps.t_alert set check_is_present = 0", $mysql_conn);
	mysql_query("
		update gps.t_alert
		set
			check_is_present = 1
		where
			id_user = '" . mysql_real_escape_string($log['id_user']) . "'
			and id_place = '" . mysql_real_escape_string($log['id_place']) . "'
			and active = 1
		
		", $mysql_conn);

	$qry_alert_status = mysql_query("
		select
			a.id_alert,
			a.id_user,
			a.id_place,
			u.username,
			p.pre_description as pre_place,
			p.description as place,
			case when a.is_present = 0 and a.check_is_present = 1 then 'is' else 'vertrok van' end as status_msg
		from 
			gps.t_alert a
			left join users.t_user u on u.id_user = a.id_user
			left join gps.t_place p on p.id_place = a.id_place
		where
			a.active = 1
			and (
				(a.is_present = 0 and a.check_is_present = 1 and a.when_entering = 1)
				or
				(a.is_present = 1 and a.check_is_present = 0 and a.when_leaving = 1)
			)
		
		", $mysql_conn);
		
	while ($alertstatus = mysql_fetch_array($qry_alert_status)) {
		$msg = $alertstatus['username'] . ' ' . $alertstatus['status_msg'] . ' ' . ($alertstatus['pre_place'] == '' ? '' : $alertstatus['pre_place'] . ' ') . $alertstatus['place'] . '';
		$channel = 'gps';
		$title = '';
		$priority = 0;
		send_msg($channel, $title, $msg, $priority);
	}

	mysql_query("update gps.t_alert set is_present = check_is_present", $mysql_conn);

}

?>