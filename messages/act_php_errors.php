<?php
set_time_limit(0);


/*
[16-Feb-2015 06:57:02 Europe/Berlin] PHP Warning:  file_get_contents(http://192.168.1.1/cgi-bin/dhcp.leases): failed to open stream: HTTP request failed!  in /var/www/router/check_network.php on line 29
[30-Apr-2015 10:25:59 Europe/Berlin] PHP Notice:  Unknown: Missing parameter (errflg=3) in Unknown on line 0
[30-Apr-2015 10:26:16 Europe/Berlin] PHP Notice:  Unknown: Unexpected characters at end of address: ; Mon, 27 Apr 2015 09:54:18 -0700 (errflg=3) in Unknown on line 0
[30-Apr-2015 11:18:44 Europe/Berlin] PHP Warning:  include(): Failed opening 'dsp_status.php' for inclusion (include_path='.:/usr/share/php:/usr/share/pear') in /var/www/router/index.php on line 35
[30-Apr-2015 14:49:30 Europe/Berlin] PHP Notice:  Undefined offset: 1 in /var/www/messages/act_php_errors.php on line 37

*/

$data = [];

$errors = @file_get_contents($settings->val('php_errorlog_file', '/var/log/php_errors.log'), "r");
if($errors !== false){
	$errors = str_replace("\r", "\n", $errors);
	//$errors = str_replace("\n\n", "\n", $errors);
	
	$lines = explode("\n", $errors);
	
	$c = count($lines);
    for ($i = 0; $i < $c; $i++) {
		if($lines[$i] != ''){
			$date = $lines[$i];
			$date = explode('[', $date)[1];
			$date = explode(']', $date)[0];
			$date = explode(' ', $date)[0] . ' ' . explode(' ', $date)[1];
			
			$severity = $lines[$i];
			$severity = explode('] PHP ', $severity)[1];
			$severity = explode(':', $severity)[0];
			
			$message = $lines[$i];
			$message = explode(':  ', $message, 2)[1];
			$message = explode(' in ', $message)[0];
			
			$location = $lines[$i];
			$location = explode(' in ', $location)[1];
			$location = explode(' on line ', $location)[0];
			
			$linenbr = $lines[$i];
			$linenbr = explode(' on line ', $linenbr)[1];
			
			$data[] = array(
				'date' => $date,
				'severity' => $severity,
				'message' => $message,
				'location' => $location,
				'linenbr' => $linenbr,
			);
		}
	}
}

/*
function my_error_handler($type, $message, $file, $line, $vars)
{
    switch($type) 
    { 
        case 1: // 1 // 
            $type_str = 'ERROR'; 
            break;
        case 2: // 2 // 
            $type_str = 'WARNING';
            break;
        case 4: // 4 // 
            $type_str = 'PARSE';
            break;
        case 8: // 8 // 
            $type_str = 'NOTICE'; 
            break;
        case 16: // 16 // 
            $type_str = 'CORE_ERROR'; 
            break;
        case 32: // 32 // 
            $type_str = 'CORE_WARNING'; 
            break;
        case 64: // 64 // 
            $type_str = 'COMPILE_ERROR'; 
            break;
        case 128: // 128 // 
            $type_str = 'COMPILE_WARNING'; 
            break;
        case 256: // 256 // 
            $type_str = 'USER_ERROR'; 
            break;
        case 512: // 512 // 
            $type_str = 'USER_WARNING'; 
            break;
        case 1024: // 1024 // 
            $type_str = 'USER_NOTICE'; 
            break;
        case 2048: // 2048 // 
            $type_str = 'STRICT'; 
            break;
        case 4096: // 4096 // 
            $type_str = 'RECOVERABLE_ERROR'; 
            break;
        case 8192: // 8192 // 
            $type_str = 'DEPRECATED'; 
            break;
        case 16384: // 16384 // 
            $type_str = 'USER_DEPRECATED'; 
            break;
    }


    $errormessage =  '[ '.date(r).' ] '.$type_str.': '.$message.' in '.$file.' on line '.$line."\n";
   // for development simply ECHO $errormessage;

        $file = 'my_errors.log';
        file_put_contents($file, $errormessage, FILE_APPEND);
}

error_reporting(E_ALL);
ini_set('display_errors', '0');
set_error_handler('my_error_handler');
*/
?>