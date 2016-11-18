<?php

$hostname = mysqli_real_escape_string($conn, saneInput('hostname'));
$username = mysqli_real_escape_string($conn, saneInput('username'));
$os = mysqli_real_escape_string($conn, saneInput('os'));

if($hostname == ''){
	$returnvalue = array('type' => 'error', 'message' => 'host name is required');
}
else if($id_user <= 0){
	$returnvalue = array('type' => 'error', 'message' => 'user is required');
}
else {
	$sql = "
		select
			h.id_host,
			h.name,
			h.username,
			h.os,
			h.date_linked_since
		
		from t_host h
		where
			h.id_user = " . $id_user . "
			and h.active = 1
			and h.name = '" . $hostname . "'
			and h.username = '" . $username . "'
		";
	
	$qry = mysqli_query($conn, $sql);
		
	if(mysqli_num_rows($qry) == 0){
		mysqli_query($conn, "
			insert into t_host (id_user, name, username, os)
			values (" . $id_user . ", '" . $hostname . "', '" . $username . "', '" . $os . "')
			");
		$qry = mysqli_query($conn, $sql);
		$returnvalue = array('type' => 'info', 'message' => 'host added', 'data' => mysql2json($qry));
		
		$script_new_host = $settings->val('script_new_host','');
		if($script_new_host != ''){
			mysqli_data_seek($qry, 0);
			$host = mysqli_fetch_array($qry);
			// and execute any scripts on completion
			$script_new_host = str_replace('%id_host%', $host['id_host'], $script_new_host);
			$script_new_host = str_replace('%name%', $host['name'], $script_new_host);
			$script_new_host = str_replace('%username%', $host['username'], $script_new_host);
			$script_new_host = str_replace('%os%', $host['os'], $script_new_host);
			
			shell_exec($script_new_host);
		}
		
	}
	else {
		$returnvalue = array('data' => mysql2json($qry));
	}
	
}

?>