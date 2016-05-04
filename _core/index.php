<?php

require '../_core/webinit.php';

switch($action->getCode()){
	
	case 'status':
		echo '<!--';
		echo date('Y-m-d H:i:s');
		print_r($app);
		print_r($action);
		echo '-->';
		break;
	
}

?>