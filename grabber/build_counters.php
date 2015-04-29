<?php
set_time_limit(3600);
include "connections.php";
include "functions.php";

$values = [];
$site = file_get_contents('http://www.electrosluts.com/site/shoots.jsp');

$target = 'href="/site/shoot.jsp?shootId=';

$index = stripos($site, $target);
while($index !== false && $index > -1){
	$item = substr($site, $index+strlen($target), 5);
	$values[] = $item;
	$index = stripos($site, $target, $index + strlen($target) + 1);
    echo 'item found: ' . $item . "<br>\n";
}


$target = 'href="/site/shoot/';

$index = stripos($site, $target);
while($index !== false && $index > -1){
	$item = substr($site, $index+strlen($target), 5);
	$values[] = $item;
	$index = stripos($site, $target, $index + strlen($target) + 1);
   echo 'item found: ' . $item . "<br>\n";
}



$v = count($values);
for($i = 0; $i < $v; $i++){
	echo $i.' => '.$values[$i] . "<br>\n";

	$qry_grabs = mysql_query("
		select
			gc.id_grab,
			gc.id_grab_counter,
			gc.listvalues
		from t_grab g
		join t_grab_counter gc on gc.id_grab = g.id_grab and gc.active = 1 and gc.type = 'list'
		where
			g.active = 1
			and (
                g.url like '%el%imagedb%'
                or
                g.url like '%kink%imagedb%'
			)
            
		", $conn);

	while ($grabs = mysql_fetch_array($qry_grabs)) {
		if(stripos(',' . $grabs['listvalues'] . ',', ',' . $values[$i] . ',') === false){
			mysql_query("
				update t_grab_counter
				set
					listvalues = '" . $grabs['listvalues'] . ',' . $values[$i] . "'
				where
					id_grab_counter = " . $grabs['id_grab_counter'] . "
				", $conn);
			
            echo $values[$i] . " inserted<br>\n";
            
			// update grab stats
			//include 'queries/pr_set_grab_stats.php';
			
		}
	}
	
	
}

?>