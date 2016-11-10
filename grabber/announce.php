<?php
set_time_limit(3600);
include "connections.php";
include "functions.php";
include '../_core/functions.php';

$url = saneInput('url');
$grab = saneInput('grab');
$file = saneInput('file');

if($url != '' && $grab != '' && $file != ''){
	
	
	$qry_grabs = mysqli_query($conn, "
		select
			g.id_grab,
			g.path,
			ifnull(gf.id_grab_file,-1) as id_grab_file,
			gf.full_url,
			ifnull(gf.status,'N') as status
		from t_grab g
		left join t_grab_file gf on gf.id_grab = g.id_grab and gf.active = 1 and gf.full_url = '" . $url . "'
		where
			g.active = 1
			and g.description like '" . $grab . "'
			
		");

	while ($grabs = mysqli_fetch_array($qry_grabs)) {
		if($grabs['id_grab_file'] > 0){
			echo 'exists:' . $grabs['status'];
		}
		else {
			mysqli_query($conn, "
				insert into t_grab_file
				(
					id_grab,
					full_url,
					full_path
				)
				values
				(
					" . $grabs['id_grab'] . ",
					'" . $url . "',
					'" . $grabs['path'] . $file . "'
				)
				", $conn);
			echo 'new';
		}
	}
	
}

	
?>