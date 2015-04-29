<?php

$qry_settings = mysql_query("
	select
		s.code,
		s.value
	from t_setting s 
	where
		s.active = 1
	", $conn);
	
while ($settingrow = mysql_fetch_array($qry_settings)) {
	eval('$setting_' . $settingrow{'code'} . ' = "' . $settingrow{'value'} . '";');
}

?>