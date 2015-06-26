<?php

$hostname = mysql_real_escape_string(saneInput('hostname'));
$username = mysql_real_escape_string(saneInput('username'));
$os = mysql_real_escape_string(saneInput('os'));

if($hostname == ''){
	$returnvalue = array('type' => 'error', 'message' => 'host name is required');
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
			h.active = 1
			and h.name = '" . $hostname . "'
			and h.username = '" . $username . "'
		";
	
	$qry = mysql_query($sql, $conn);
		
	if(mysql_num_rows($qry) == 0){
		mysql_query("
			insert into t_host (name, username, os)
			values ('" . $hostname . "', '" . $username . "', '" . $os . "')
			", $conn);
		$qry = mysql_query($sql, $conn);
		$returnvalue = array('type' => 'info', 'message' => 'host added', 'data' => mysql2json($qry));
		
		$script_new_host = $settings->val('script_new_host','');
		if($script_new_host != ''){
			mysql_data_seek($qry, 0);
			$host = mysql_fetch_array($qry);
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