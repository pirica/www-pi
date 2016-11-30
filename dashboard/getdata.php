<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

require dirname(__FILE__).'/../_core/components/phpQuery/phpQuery.php';

include 'connection.php';
include 'functions.php';


$tijden_end = date("Ymd", strtotime('-1 week', time()));

mysqli_query($conn, "
	delete from reistijden
	where
		DATE_FORMAT(date, '%Y%m%d') < '" . $tijden_end . "'
");


$str_reistijden = file_get_contents('http://www.verkeerscentrum.be/verkeersinfo/reistijden/E313');
$reistijden = phpQuery::newDocumentHTML($str_reistijden);

$etappes = array('go-gw','gw-ho','ho-hi','hi-hw','hw-m','m-r','r-w','w-ao',
				'','ao-w','w-r','r-m','m-hw','hw-hi','hi-ho','ho-gw','gw-go');
$i_etappe = 0;

foreach (pq($reistijden)->find('#container span') as $tijdspan)
{
    $tijd = pq($tijdspan)->text();
	
	if($etappes[$i_etappe] != '')
	{
		mysqli_query($conn, "
			insert into reistijden
			(
				knooppuntId,
				value
			)
			values
			(
				'" . mysqli_real_escape_string($conn, $etappes[$i_etappe]) . "',
				'" . mysqli_real_escape_string($conn, $tijd) . "'
			)
		");
	}
	$i_etappe++;
}


$str_reistijden = file_get_contents('http://www.verkeerscentrum.be/verkeersinfo/reistijden/binnenring');
$reistijden = phpQuery::newDocumentHTML($str_reistijden);

$etappes = array('','ao-az','az-ac','','','','');
$i_etappe = 0;

foreach (pq($reistijden)->find('#container span') as $tijdspan)
{
    $tijd = pq($tijdspan)->text();
	
	if($etappes[$i_etappe] != '')
	{
		mysqli_query($conn, "
			insert into reistijden
			(
				knooppuntId,
				value
			)
			values
			(
				'" . mysqli_real_escape_string($conn, $etappes[$i_etappe]) . "',
				'" . mysqli_real_escape_string($conn, $tijd) . "'
			)
		");
	}
	$i_etappe++;
}


$str_reistijden = file_get_contents('http://www.verkeerscentrum.be/verkeersinfo/reistijden/buitenring');
$reistijden = phpQuery::newDocumentHTML($str_reistijden);

$etappes = array('','','','','ac-az','az-ao','');
$i_etappe = 0;

foreach (pq($reistijden)->find('#container span') as $tijdspan)
{
    $tijd = pq($tijdspan)->text();
	
	if($etappes[$i_etappe] != '')
	{
		mysqli_query($conn, "
			insert into reistijden
			(
				knooppuntId,
				value
			)
			values
			(
				'" . mysqli_real_escape_string($conn, $etappes[$i_etappe]) . "',
				'" . mysqli_real_escape_string($conn, $tijd) . "'
			)
		");
	}
	$i_etappe++;
}


?>