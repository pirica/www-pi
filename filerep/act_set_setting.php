<?php

$code = mysql_real_escape_string(saneInput('code'));
$value = mysql_real_escape_string(saneInput('value'));

if($code == ''){
	$returnvalue = array('type' => 'error', 'message' => 'incorrect code');
}
else {
	
	mysql_query("
		insert into t_setting_host (code, value, id_host)
		select s.code, s.value, h.id_host
		from 
			t_host h
			join t_setting s on 1=1
			left join t_setting_host sh on sh.id_host = h.id_host and sh.code = s.code 
		where
			sh.id_setting_host is null
		", $conn);
	/*
	mysql_query("
		insert into t_setting_host (code, value, id_host)
		select s.code, s.value, h.id_host
		from 
			t_host h
			join t_setting s on 1=1
			left join t_setting_host sh on sh.id_host = h.id_host and sh.code = s.code 
		where
			h.id_host = " . $id_host . " 
			and s.code = '" . $code . "'
			and sh.id_setting_host is null
		", $conn);
	*/
	$qry = mysql_query("
		update t_setting_host 
		set
			value = '" . $value . "'
		where
			code = '" . $code . "'
			and active = 1
			and id_host = " . $id_host . " 
		", $conn);
		
	$returnvalue = array('type' => 'info', 'message' => 'setting \''.$code.'\' updated to \''.$value.'\' for id '. $id_host);

}



?>