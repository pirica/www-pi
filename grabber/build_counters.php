<?php
set_time_limit(3600);
include "connections.php";
include "functions.php";

//$site = file_get_contents('http://www.electrosluts.com/site/shoots.jsp');
//$site = file_get_contents('http://www.kink.com/channel/electrosluts');
$site = file_get_contents('http://www.kink.com/site/electrosluts');
echo $site;

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/cookieFileName");
curl_setopt($ch, CURLOPT_URL,"http://www.myterminal.com/checkpwd.asp");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "UserID=username&password=passwd");

ob_start();      // prevent any output
curl_exec ($ch); // execute the curl command
ob_end_clean();  // stop preventing output

curl_close ($ch);
unset($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookieFileName");
curl_setopt($ch, CURLOPT_URL,"http://www.myterminal.com/list.asp");

$buf2 = curl_exec ($ch);

curl_close ($ch);

echo $buf2
*/

$values = [];

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


$target = 'href="/shoot/';

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

	$qry_grabs = mysqli_query($conn, "
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
            
		");

	while ($grabs = mysqli_fetch_array($qry_grabs)) {
		if(stripos(',' . $grabs['listvalues'] . ',', ',' . $values[$i] . ',') === false){
			mysqli_query($conn, "
				update t_grab_counter
				set
					listvalues = '" . $grabs['listvalues'] . ',' . $values[$i] . "'
				where
					id_grab_counter = " . $grabs['id_grab_counter'] . "
				");
			
            echo $values[$i] . " inserted<br>\n";
            
			// update grab stats
			//include 'queries/pr_set_grab_stats.php';
			
		}
	}
	
	
}

?>