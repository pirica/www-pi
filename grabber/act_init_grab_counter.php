<?php
$id_grab_counter = saneInput('id_grab_counter', 'int', -1);
$error = 0;

$counter_type = '';
$counter_field = '';

$counter_datefrom = '';
$counter_dateto = '';

$counter_intfrom = '';
$counter_intto = '';

$counter_listvalues = '';

while($counter = mysql_fetch_array($qry_grab_counts)){
	if($counter['id_grab_counter'] == $id_grab_counter){
		$counter_type = $counter['type'];
		$counter_field = $counter['field'];
		
		$counter_datefrom = $counter['datefrom'];
		$counter_dateto = $counter['dateto'];
		
		$counter_intfrom = $counter['intfrom'];
		$counter_intto = $counter['intto'];
		
		$counter_listvalues = $counter['listvalues'];
	}
}

?>