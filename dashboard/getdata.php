<?php
set_time_limit(3600);
require dirname(__FILE__).'/../_core/appinit.php';

require dirname(__FILE__).'/../_core/components/phpQuery/phpQuery.php';

include 'connection.php';
include 'functions.php';


$str_reistijden_e313 = file_get_contents('http://www.verkeerscentrum.be/verkeersinfo/reistijden/E313');

$reistijden_e313 = phpQuery::newDocumentHTML($str_reistijden_e313);

$tijden = $reistijden_e313->find('#container span');

$etappes = array('go-gw','gw-ho','ho-hi','hi-hw','hw-m','m-r','r-w','w-ao',
				'','ao-w','w-r','r-m','m-hw','hw-hi','hi-ho','ho-gw','gw-go');

$i_etappe = 0;

foreach ($tijden as $tijdspan) {
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