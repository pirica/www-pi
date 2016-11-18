<?php
set_time_limit(0);

/*
select convert( convert(date_log, char(10)) , datetime) as date_log_flat
from t_file_log 
group by convert( convert(date_log, char(10)) , datetime) 
*/

$qry = mysqli_query($conn, "
	delete from t_file_log_summary
	where
		date_log_flat = convert( convert(now(), char(10)) , datetime)
	");
	
	
$qry = mysqli_query($conn, "
	
	insert into t_file_log_summary
	(
		date_log_flat,
		id_host,
		id_share,
		id_file,
		json
	)
	
	select
		convert( convert(fl.date_log, char(10)) , datetime) as date_log_flat,
		fl.id_host,
		s.id_share,
		f.id_file,
		
		CONCAT(
			CONCAT('{\"id_host\":',fl.id_host,''),
			CONCAT(',\"id_share\":',s.id_share,''),
			
			CONCAT(',\"host_name\":\"',h.name,'\"'),
			CONCAT(',\"share_name\":\"',s.name,'\"'),
			CONCAT(',\"relative_directory\":\"',f.relative_directory,'\"'),
			CONCAT(',\"filename\":\"',f.filename,'\"'),
			CONCAT(',\"date_log\":\"',fl.date_log,'\"'),
			CONCAT(',\"text_log\":\"',ifnull(fl.text_log,''),'\"'),
			CONCAT(',\"size\":',ifnull(fl.size,-1),''),
			CONCAT(',\"version\":',ifnull(fl.version,-1),''),
			CONCAT(',\"date_last_modified\":\"',ifnull(fl.date_last_modified,''),'\"}')
		) AS json
		
	from t_file_log fl
		join t_host h on h.id_host = fl.id_host
		join t_file f on f.id_file = fl.id_file
		join t_share s on s.id_share = f.id_share
	where
		fl.active = 1
		and convert( convert(fl.date_log, char(10)) , datetime) = convert( convert(now() - interval 1 day, char(10)) , datetime)
		#and fl.date_log between 
		#	convert( convert(now(), char(10)) , datetime)
		#	and
		#	convert( convert(now() + interval 1 day, char(10)) , datetime)
	#group by
	#	convert( convert(fl.date_log, char(10)) , datetime),
	#	fl.id_host,
	#	s.id_share
	");
	
?>

