
insert into t_usage_month
(
	usagekey,
	mac_address,
	date_usage,
	downloaded,
	uploaded,
	downloaded_telemeter,
	uploaded_telemeter
)
select
	concat(h.mac_address , '2014-11-04' ) as usagekey,
	h.mac_address,
	'2014-11-04' as date_usage,
	0,
	0,
	0,
	0
from 
t_host h
left join t_usage_month u on u.usagekey = concat(h.mac_address , '2014-11-04' )

where
	h.active = 1
	and u.usagekey is null
;


insert into t_usage_month
(
	usagekey,
	mac_address,
	date_usage,
	downloaded,
	uploaded,
	downloaded_telemeter,
	uploaded_telemeter
)
select
	concat(h.mac_address , '2014-10-04' ) as usagekey,
	h.mac_address,
	'2014-10-04' as date_usage,
	0,
	0,
	0,
	0
from 
t_host h
left join t_usage_month u on u.usagekey = concat(h.mac_address , '2014-10-04' )

where
	h.active = 1
	and u.usagekey is null
;


insert into t_usage_day
(
	usagekey,
	mac_address,
	date_usage,
	downloaded,
	uploaded,
	downloaded_telemeter,
	uploaded_telemeter
)
select
	concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d') ) as usagekey,
	h.mac_address,
	d.date as date_usage,
	0,
	0,
	0,
	0
from 
(
	SELECT (str_to_date('20141004', '%Y%m%d')+INTERVAL (H+T+U) day) date
	FROM ( SELECT 0 H
		UNION ALL SELECT 100 UNION ALL SELECT 200 UNION ALL SELECT 300
	  ) H 
	  CROSS JOIN ( SELECT 0 T
		UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
		UNION ALL SELECT  40 UNION ALL SELECT  50 UNION ALL SELECT  60
		UNION ALL SELECT  70 UNION ALL SELECT  80 UNION ALL SELECT  90
	  ) T 
	  CROSS JOIN ( SELECT 0 U
		UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
		UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
		UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
	  ) U
	WHERE
		DATE_FORMAT((str_to_date('20141004', '%Y%m%d')+INTERVAL (H+T+U) day), '%Y%m%d') < '20141114'

) d
cross join (
	select mac_address from t_host
	where active = 1
	group by mac_address
) h
left join t_usage_day u on u.usagekey = concat(h.mac_address , DATE_FORMAT(d.date, '%Y-%m-%d') )
where
	u.usagekey is null
;






replace into t_usage_month (usagekey, mac_address, date_usage, downloaded_telemeter)
select
	'xx:xx:xx:xx:xx:xx2014-11-04' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-04' as date_usage,
	(102.4 * 1024 * 1024 * 1024) - sum(downloaded_telemeter + uploaded_telemeter) as downloaded_telemeter
from t_usage_month
where 
	date_usage = '2014-11-04'
	and mac_address <> 'xx:xx:xx:xx:xx:xx'
;



replace into t_usage_day (usagekey, mac_address, date_usage, downloaded_telemeter)
select
	'xx:xx:xx:xx:xx:xx2014-11-04' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-04' as date_usage,
	(2914 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-05' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-05' as date_usage,
	(8840 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-06' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-06' as date_usage,
	(14223 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-07' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-07' as date_usage,
	(19023 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-08' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-08' as date_usage,
	(15383 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-09' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-09' as date_usage,
	(23720 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-11' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-11' as date_usage,
	(217 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-13' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-13' as date_usage,
	(87 * 1024 * 1024) as downloaded_telemeter
;










replace into t_usage_month (usagekey, mac_address, date_usage, downloaded_telemeter)
select
	'xx:xx:xx:xx:xx:xx2014-10-04' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-04' as date_usage,
	(7.232 * 1024 * 1024 * 1024) as downloaded_telemeter
;



replace into t_usage_day (usagekey, mac_address, date_usage, downloaded_telemeter)
select
	'xx:xx:xx:xx:xx:xx2014-10-04' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-04' as date_usage,
	(88 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-05' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-05' as date_usage,
	(264 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-06' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-06' as date_usage,
	(153 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-07' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-07' as date_usage,
	(303 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-08' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-08' as date_usage,
	(149 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-09' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-09' as date_usage,
	(160 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-10' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-10' as date_usage,
	(119 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-11' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-11' as date_usage,
	(98 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-12' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-12' as date_usage,
	(366 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-13' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-13' as date_usage,
	(224 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-14' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-14' as date_usage,
	(217 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-15' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-15' as date_usage,
	(151 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-16' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-16' as date_usage,
	(240 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-17' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-17' as date_usage,
	(102 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-18' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-18' as date_usage,
	(96 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-19' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-19' as date_usage,
	(360 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-20' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-20' as date_usage,
	(178 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-21' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-21' as date_usage,
	(634 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-22' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-22' as date_usage,
	(279 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-23' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-23' as date_usage,
	(61 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-24' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-24' as date_usage,
	(265 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-25' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-25' as date_usage,
	(169 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-26' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-26' as date_usage,
	(294 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-27' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-27' as date_usage,
	(209 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-28' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-28' as date_usage,
	(386 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-29' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-29' as date_usage,
	(543 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-30' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-30' as date_usage,
	(79 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-10-31' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-10-31' as date_usage,
	(64 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-01' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-01' as date_usage,
	(296 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-02' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-02' as date_usage,
	(96 * 1024 * 1024) as downloaded_telemeter
union
select
	'xx:xx:xx:xx:xx:xx2014-11-03' as usagekey,
	'xx:xx:xx:xx:xx:xx' as mac_address,
	'2014-11-03' as date_usage,
	(587 * 1024 * 1024) as downloaded_telemeter
;