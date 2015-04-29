<?php
/*

delete from t_log_message where id_log_message in (
select id from (
select 
	count(id_log_message) as nbr,
	min(id_log_message) as id,
	min(date_sent) as min_date,
	channel, title, message
from t_log_message
group by
	channel, title, message
having 
	count(id_log_message) > 1
)m)
and date_sent < now() - interval 1 month;

*/

/*
delete from t_log_message where id_log_message not in (
select id from (
select 
	count(id_log_message) as nbr,
	max(id_log_message) as id,
	max(date_sent) as max_date,
	channel, title, message
from t_log_message
group by
	channel, title, message
)m)
and date_sent < now() - interval 1 month;

delete from t_log_message where date_sent < now() - interval 366 day;

*/
?>