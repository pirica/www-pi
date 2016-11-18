<?php

$code = mysqli_real_escape_string($conn, saneInput('code'));
$value = mysqli_real_escape_string($conn, saneInput('value'));

if($code == ''){
	$returnvalue = array('type' => 'error', 'message' => 'incorrect code');
}
else {
	
	mysqli_query($conn, "
		insert into t_setting_host (code, value, id_host)
		select s.code, s.value, h.id_host
		from 
			t_host h
			join t_setting s on 1=1
			left join t_setting_host sh on sh.id_host = h.id_host and sh.code = s.code 
		where
			sh.id_setting_host is null
		");
	
	$qry = mysqli_query($conn, "
		update t_setting_host 
		set
			value = '" . $value . "'
		where
			code = '" . $code . "'
			and active = 1
			and id_host = " . $id_host . " 
		");
		
	$returnvalue = array('type' => 'info', 'message' => 'setting \''.$code.'\' updated to \''.$value.'\' for id '. $id_host);

}



?>