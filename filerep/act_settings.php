<?php

$qry_settings = mysqli_query($conn, "
	select
		s.code,
		s.value
	from t_setting s 
	where
		s.active = 1
	");
	
while ($settingrow = mysqli_fetch_array($qry_settings)) {
	eval('$setting_' . $settingrow{'code'} . ' = "' . $settingrow{'value'} . '";');
}

?>