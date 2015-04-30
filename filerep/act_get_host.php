<?php

$hostname = mysql_real_escape_string(saneInput('hostname'));
$os = mysql_real_escape_string(saneInput('os'));

if($hostname == ''){
	$returnvalue = array('type' => 'error', 'message' => 'host name is required');
}
else {
	/*
	SELECT 
		CONCAT(
			'[',
			GROUP_CONCAT(
				CONCAT('{"id_host":"',id_host,'"'),
				CONCAT(',"name":"',name,'"'),
				CONCAT(',"os":"',os,'"'),
				CONCAT(',"date_linked_since":"',date_linked_since,'"}')
			)
			,']'
		) AS json 
	FROM users;
	*/
	
	$sql = "
		select
			h.id_host,
			h.name,
			h.os,
			h.date_linked_since,
			CONCAT(
				'[',
				GROUP_CONCAT(
					CONCAT('{\"id_host\":\"',id_host,'\"'),
					CONCAT(',\"name\":\"',name,'\"'),
					CONCAT(',\"os\":\"',os,'\"'),
					CONCAT(',\"date_linked_since\":\"',date_linked_since,'\"}')
				)
				,']'
			) AS json 
		
		from t_host h
		where
			h.active = 1
			and h.name = '" . $hostname . "'
		";
	
	$qry = mysql_query($sql, $conn);
		
	if(mysql_num_rows($qry) == 0){
		mysql_query("
			insert into t_host (name, os)
			values ('" . $hostname . "', '" . $os . "')
			", $conn);
		$qry = mysql_query($sql, $conn);
		$returnvalue = array('type' => 'info', 'message' => 'host added', 'data' => mysql2json($qry));
	}
	else {
		$returnvalue = array('data' => mysql2json($qry));
	}
	
}

?>